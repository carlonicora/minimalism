<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\helpers\errorReporter;
use Exception;

abstract class abstractController {
    /** @var string */
    protected $modelName;

    /** @var abstractConfigurations */
    protected $configurations;

    /** @var abstractModel */
    protected $model;

    /** @var array */
    protected $parameterValues;

    /** @var array */
    protected $parameterValueList;

    /** @var  */
    protected $file;

    /**
     * abstractController constructor.
     * @param $configurations
     * @param null $modelName
     * @param null $parameterValueList
     * @param null $parameterValues
     */
    public function __construct($configurations, $modelName=null, $parameterValueList=null, $parameterValues=null){
        $this->configurations = $configurations;

        if (!empty($parameterValueList) || !empty($parameterValues)) {
            $this->parameterValueList = $parameterValueList;
            $this->parameterValues = $parameterValues;
        } else {
            $this->initialiseParameters();
        }

        if (isset($modelName)) {
            $this->modelName = $modelName;
        }

        $this->initialiseModel();
    }

    abstract public function render(): string;

    /**
     *
     */
    protected function initialiseParameters(): void {
        $this->modelName = 'index';
        $this->parameterValues = array();
        $this->parameterValueList = array();

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

        if ($this->verb === 'GET') {
            foreach ($_GET as $parameter => $value) {
                if ($parameter !== 'path' && $parameter !== 'XDEBUG_SESSION_START') {
                    $this->parameterValues[$parameter] = $value;
                }
            }
        } else {
            $input = file_get_contents('php://input');

            if (!empty($input)) {
                try {
                    $this->parameterValues = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
                } catch (Exception $e) {
                    $this->parameterValues = null;
                }
            }

            if (isset($_FILES) && count($_FILES) === 1) {
                $this->file = array_values($_FILES)[0];
            }

            if (!isset($this->parameterValues)) {
                foreach ($_POST as $parameter => $value) {
                    $this->parameterValues[$parameter] = $value;
                }
            }
        }
    }

    /**
     *
     */
    private function initialiseModel(): void {
        $this->modelName = str_replace('-', '\\', $this->modelName);

        $namespaces = explode('\\', get_class($this->configurations));
        array_pop($namespaces);
        $namespaces[] = 'models';
        $namespaces[] = $this->modelName;
        $modelClass = implode('\\', $namespaces);

        if (!class_exists($modelClass)){
            errorReporter::report($this->configurations, 3, null, 404);
        } else {
            $this->model = new $modelClass($this->configurations, $this->parameterValues, $this->parameterValueList, $this->file, $this->verb);
        }

        if ($this->model->redirect() !== ''){
            $this->modelName = $this->model->redirect();
            $this->initialiseModel();
        }
    }
}

/**
 *
 */
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