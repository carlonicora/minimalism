<?php
namespace carlonicora\minimalism\controllers;

use carlonicora\minimalism\abstracts\abstractController;
use carlonicora\minimalism\helpers\errorReporter;
use carlonicora\minimalism\helpers\security;

class apiController extends abstractController {
    /** @var string */
    private $signature;

    /** @var string */
    public $verb;

    /**
     * apiController constructor.
     * @param $configurations
     * @param null $modelName
     * @param null $parameterValueList
     * @param null $parameterValues
     */
    public function __construct($configurations, $modelName = null, $parameterValueList = null, $parameterValues = null) {
        $this->initialiseVerb();

        parent::__construct($configurations, $modelName, $parameterValueList, $parameterValues);

        $this->validateSignature();
    }

    /**
     * @inheritDoc
     */
    protected function getHttpType(): string {
        return $this->verb;
    }

    /**
     *
     */
    protected function initialiseVerb(): void {
        $this->verb = $_SERVER['REQUEST_METHOD'];
        if ($this->verb === 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'DELETE') {
                $this->verb = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PUT') {
                $this->verb = 'PUT';
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function initialiseModel(string $modelName = null): void {
        if (isset($modelName)) {
            $this->modelName = str_replace('-', '\\', $modelName);
        }

        $configurationClassName = get_class($this->configurations);
        $lastDashPosition = strrpos($configurationClassName, '\\');
        $modelClass = substr_replace($configurationClassName, '\\models\\' . $this->modelName, $lastDashPosition);

        if (!class_exists($modelClass)){
            errorReporter::report($this->configurations, 3, null, 404);
        }

        $this->model = new $modelClass($this->configurations, $this->parameterValues, $this->parameterValueList, $this->verb, $this->file);

        if ($this->model->redirect() !== ''){
            $this->initialiseModel($this->model->redirect());
        }
    }

    /**
     *
     */
    protected function validateSignature(): void {
        $headers = getallheaders();
        $this->signature = $headers[$this->configurations->httpHeaderSignature] ?? null;

        $security = new security($this->configurations);
        $url = $_SERVER['REQUEST_URI'];

        if (!$security->validateSignature($this->signature, $this->verb, $url,  $this->parameterValues, $this->configurations->getSecurityClient(), $this->configurations->getSecuritySession())){
            errorReporter::report($this->configurations,  11, 'Failure in validating signature', 401);
        }
    }

    /**
     * @return string
     */
    public function render(): string{
        $data = $this->model->{$this->verb}();

        return json_encode($data, JSON_THROW_ON_ERROR, 512);
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