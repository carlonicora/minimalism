<?php
namespace carlonicora\minimalism;

use carlonicora\minimalism\abstracts\configurations;
use carlonicora\minimalism\abstracts\model;
use carlonicora\minimalism\helpers\errorReporter;
use carlonicora\minimalism\helpers\security;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class controller {
    /** @var string */
    private $modelName;

    /** @var configurations */
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

    public function __construct($configurations, $modelName=null, $parameters=null){
        $this->initialiseVerb();

        $this->configurations = $configurations;

        if (isset($parameters)) {
            $this->parameterValueList = $parameters;
        } else {
            $this->initialiseParameters();
        }

        if (isset($modelName)) $this->modelName = $modelName;

        if ($this->configurations->applicationType == configurations::MINIMALISM_API){
            $this->validateSignature();
        }

        $this->initialiseModel();
        $this->initialiseView();
    }

    public function render(){
        $data = array();
        if ($this->configurations->applicationType == configurations::MINIMALISM_APP){
            $data['baseUrl'] = $this->configurations->getBaseUrl();
            $data['page'] = $this->model->generateData();
        } else {
            $data = $this->model->{$this->verb}();
        }

        if (array_key_exists('forceRedirect', $data)){
            header('Location:' . $data['forceRedirect']);
            exit;
        }

        switch ($this->configurations->applicationType){
            case configurations::MINIMALISM_API:
                $returnValue = json_encode($data);
                break;
            case configurations::MINIMALISM_APP:
            default:
                if ($this->model->getViewName() != ''){
                    try {
                        $returnValue = $this->view->render($data);
                    } catch (LoaderError $e) {
                        $returnValue = '';
                    } catch (RuntimeError $e) {
                        $returnValue = '';
                    } catch (SyntaxError $e) {
                        $returnValue = '';
                    }
                } else {
                    $returnValue = json_encode($data);
                }
                break;
        }

        if ($returnValue && $this->configurations->applicationType == configurations::MINIMALISM_APP){
            $_SESSION['configurations'] = $this->configurations;
        }

        return($returnValue);
    }

    private function initialiseVerb(){
        $this->verb = $_SERVER['REQUEST_METHOD'];
        if ($this->verb == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->verb = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->verb = 'PUT';
            }
        }
    }

    private function validateSignature(){
        $headers = getallheaders();
        $this->signature = isset($headers["minimalism-signature"]) ? $headers["minimalism-signature"] : null;

        $security = new security($this->configurations);
        $url = ($_SERVER['SERVER_PORT'] == '80' ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (!$security->validateSignature($this->signature, $this->verb, $url,  $this->parameterValues)){
            errorReporter::report($this->configurations,  11, 'Failure in validating signature', 401);
        }
    }

    private function initialiseParameters(){
        $this->modelName = 'index';
        $this->parameterValues = array();
        $this->parameterValueList = array();

        $uri = strtok($_SERVER["REQUEST_URI"],'?');

        if (!(isset($uri) && strlen($uri) == 1 && $uri == '/')) {
            $variables = array_filter(explode('/', substr($uri, 1)), 'strlen');

            $isModelVariable = true;
            foreach($variables as $variable){
                if ($isModelVariable &&!(is_numeric($variable))){
                    $this->modelName = $variable;
                } else {
                    $this->parameterValueList[] = $variable;
                }
                $isModelVariable = false;
            }
        }

        switch ($this->verb){
            case 'DELETE':
            case 'POST':
            case 'PUT':
                $this->parameterValues = json_decode(file_get_contents("php://input"), true);

                if (isset($_FILES) && sizeof($_FILES) == 1){
                    $this->file = array_values($_FILES)[0];
                }

                if (!isset($this->parameterValues)) {
                    foreach ($_POST as $parameter => $value) {
                        $this->parameterValues[$parameter] = $value;
                    }
                }
                break;
            case 'GET':
                foreach ($_GET as $parameter=>$value){
                    if ($parameter != 'path' && $parameter != 'XDEBUG_SESSION_START') $this->parameterValues[$parameter] = $value;
                }
                break;
        }
    }

    private function initialiseModel(){
        $this->modelName = str_replace('-', '\\', $this->modelName);

        $modelClass = $this->configurations->getNamespace() . '\\models\\' . $this->modelName;
        if (!class_exists($modelClass)){
            errorReporter::report($this->configurations, 3, null, 404);
        } else {
            $this->model = new $modelClass($this->configurations, $this->parameterValues, $this->parameterValueList);
        }

        if ($this->model->redirect() != false){
            $this->modelName = $this->model->redirect();
            $this->initialiseModel();
        }

        if ($this->configurations->applicationType == configurations::MINIMALISM_API){
            if($this->model->requiresAuth($this->verb)){
                // TODO
            }
        }
    }

    private function initialiseView(){
        if ($this->model->getViewName() != '') {
            try {
                $twigLoader = new FilesystemLoader($this->configurations->appDirectory . DIRECTORY_SEPARATOR . 'views');
                $this->view = new Environment($twigLoader);
            } catch (Exception $exception) {
                errorReporter::report($this->configurations, 4, null, 404);
            }

            try {
                $this->view = $this->view->load($this->model->getViewName() . '.twig');
            } catch (LoaderError $e) {
                $this->view = null;
            } catch (RuntimeError $e) {
                $this->view = null;
            } catch (SyntaxError $e) {
                $this->view = null;
            }

            if (!$this->view) {
                errorReporter::report($this->configurations, 5, null, 404);
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
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}