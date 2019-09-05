<?php
namespace carlonicora\minimalism;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\abstracts\model;
use carlonicora\minimalism\helpers\errorReporter;
use carlonicora\minimalism\helpers\security;
use carlonicora\minimalism\helpers\sessionManager;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class controller {
    /** @var string */
    private $modelName;

    /** @var abstractConfigurations */
    private $configurations;

    /** @var model */
    private $model;

    /** @var Environment */
    private $view;

    /** @var array */
    private $parameterValues;

    /** @var array */
    private $parameterValueList;

    /** @var  */
    private $file;

    /** @var string */
    public $verb;

    /** @var string */
    private $signature;

    public function __construct($configurations, $modelName=null, $parameterValueList=null, $parameterValues=null){
        $this->configurations = $configurations;

        if ($this->configurations->applicationType !== abstractConfigurations::MINIMALISM_CLI) {
            $this->initialiseVerb();
        }

        if (!empty($parameterValueList) || !empty($parameterValues)) {
            $this->parameterValueList = $parameterValueList;
            $this->parameterValues = $parameterValues;
        } else {
            $this->initialiseParameters();
        }

        if (isset($modelName)) {
            $this->modelName = $modelName;
        }

        if ($this->configurations->applicationType === abstractConfigurations::MINIMALISM_API && $this->modelName !== 'index'){
            $this->validateSignature();
        }

        $this->initialiseModel();
        $this->initialiseView();
    }

    public function render(){
        $data = array();

        $response = true;

        switch ($this->configurations->applicationType){
            case abstractConfigurations::MINIMALISM_APP:
                $data['baseUrl'] = $this->configurations->getBaseUrl();
                $data['page'] = $this->model->generateData();
                break;
            case abstractConfigurations::MINIMALISM_API:
                $data = $this->model->{$this->verb}();
                break;
            case abstractConfigurations::MINIMALISM_CLI:
                $response = $this->model->generateData();
                break;
        }

        switch ($this->configurations->applicationType){
            case abstractConfigurations::MINIMALISM_API:
                $response = json_encode($data);
                break;
            case abstractConfigurations::MINIMALISM_APP:
                if (array_key_exists('forceRedirect', $data)){
                    header('Location:' . $data['forceRedirect']);
                    exit;
                }

                if ($this->model->getViewName() !== ''){
                    try {
                        $response = $this->view->render($this->model->getViewName() . '.twig', $data);
                    } catch (Exception $e){
                        $response = '';
                    }
                } else {
                    $response = json_encode($data);
                }
                break;
        }

        if ($response && $this->configurations->applicationType === abstractConfigurations::MINIMALISM_APP){
            $sessionManager = new sessionManager();
            $sessionManager->saveSession($this->configurations);
        }

        return $response;
    }

    private function initialiseVerb(): void
    {
        $this->verb = $_SERVER['REQUEST_METHOD'];
        if ($this->verb === 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'DELETE') {
                $this->verb = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PUT') {
                $this->verb = 'PUT';
            }
        }
    }

    private function validateSignature(): void
    {
        $headers = getallheaders();
        $this->signature = $headers[$this->configurations->httpHeaderSignature] ?? null;

        $security = new security($this->configurations);
        //$url = ($_SERVER['SERVER_PORT'] == '80' ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = $_SERVER['REQUEST_URI'];
        if (!$security->validateSignature($this->signature, $this->verb, $url,  $this->parameterValues)){
            errorReporter::report($this->configurations,  11, 'Failure in validating signature', 401);
        }
    }

    private function initialiseParameters(): void
    {
        $this->modelName = 'index';
        $this->parameterValues = array();
        $this->parameterValueList = array();

        if ($this->configurations->applicationType === abstractConfigurations::MINIMALISM_CLI){
            if (isset($_SERVER['argv'][1]) && !isset($_SERVER['argv'][2])){
                $this->parameterValues = json_decode($_SERVER['argv'][1], true);
            } else if (count($_SERVER['argv']) > 1){
                for ($argumentCount = 1, $argumentCountMax = count($_SERVER['argv']); $argumentCount < $argumentCountMax; $argumentCount += 2){
                    $this->parameterValues[substr($_SERVER['argv'][$argumentCount], 1)] = $_SERVER['argv'][$argumentCount + 1];
                }
            }
        } else {
            $uri = strtok($_SERVER['REQUEST_URI'], '?');

            if (!(isset($uri) && $uri === '/')) {
                $variables = array_filter(explode('/', substr($uri, 1)), 'strlen');

                $isModelVariable = true;
                foreach ($variables as $variable) {
                    if ($isModelVariable && !is_numeric($variable)) {
                        $this->modelName = $variable;
                    } else {
                        $this->parameterValueList[] = $variable;
                    }
                    $isModelVariable = false;
                }
            }

            switch ($this->verb) {
                case 'POST':
                case 'PUT':
                    $this->parameterValues = json_decode(file_get_contents('php://input'), true);

                    if (isset($_FILES) && count($_FILES) === 1) {
                        $this->file = array_values($_FILES)[0];
                    }

                    if (!isset($this->parameterValues)) {
                        foreach ($_POST as $parameter => $value) {
                            $this->parameterValues[$parameter] = $value;
                        }
                    }
                    break;
                case 'DELETE':
                case 'GET':
                    foreach ($_GET as $parameter => $value) {
                        if ($parameter !== 'path' && $parameter !== 'XDEBUG_SESSION_START') {
                            $this->parameterValues[$parameter] = $value;
                        }
                    }
                    break;
            }
        }
    }

    private function initialiseModel(): void
    {
        $this->modelName = str_replace('-', '\\', $this->modelName);

        $namespaces = explode('\\', get_class($this->configurations));
        array_pop($namespaces);
        $namespaces[] = 'models';
        $namespaces[] = $this->modelName;
        $modelClass = implode('\\', $namespaces);

        if (!class_exists($modelClass)){
            errorReporter::report($this->configurations, 3, null, 404);
        } else {
            $this->model = new $modelClass($this->configurations, $this->parameterValues, $this->parameterValueList, $this->file);
        }

        if ($this->model->redirect() !== ''){
            $this->modelName = $this->model->redirect();
            $this->initialiseModel();
        }

        /*
        if ($this->configurations->applicationType == configurations::MINIMALISM_API){
            if($this->model->requiresAuth($this->verb)){
                // TODO
            }
        }
        */
    }

    private function initialiseView(): void
    {
        if ($this->model->getViewName() !== '') {
            try {
                $twigLoader = new FilesystemLoader($this->configurations->appDirectory . DIRECTORY_SEPARATOR . 'views');
                $this->view = new Environment($twigLoader);
            } catch (Exception $exception) {
                errorReporter::report($this->configurations, 4, null, 404);
            }
        }
    }
}

if (!function_exists('getallheaders'))  {
    function getallheaders()
    {
        if (!is_array($_SERVER)) {
            return array();
        }

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}