<?php
namespace carlonicora\minimalism\controllers;

use carlonicora\minimalism\abstracts\abstractController;
use carlonicora\minimalism\helpers\errorReporter;
use carlonicora\minimalism\helpers\headers;
use carlonicora\minimalism\helpers\security;
use carlonicora\minimalism\interfaces\responseInterface;
use carlonicora\minimalism\jsonapi\responses\dataResponse;

class apiController extends abstractController {
    /** @var string */
    private string $signature;

    /** @var string */
    public string $verb;

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
    protected function initialiseModel(string $modelName = null, string $verb=null): void {
        parent::initialiseModel($modelName, $this->verb);
    }

    /**
     *
     */
    protected function validateSignature(): void {
        $this->signature = headers::getHeader($this->configurations->httpHeaderSignature);

        $security = new security($this->configurations);
        $url = $_SERVER['REQUEST_URI'];

        if (!$security->validateSignature($this->signature, $this->verb, $url,  $this->parameterValues, $this->configurations->getSecurityClient(), $this->configurations->getSecuritySession())){
            errorReporter::report($this->configurations,  11, 'Failure in validating signature', 401);
        }
    }

    /**
     *
     */
    protected function parseUriParameters(): void {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');

        if ($uri !== '/') {
            $variables = array_filter(explode('/', substr($uri, 1)), 'strlen');

            $firstVariable = current($variables);
            if (stripos($firstVariable, 'v') === 0 && is_numeric(substr($firstVariable, 1, 1)) && strpos($firstVariable, '.') !== 0 ) {
                $this->version = $firstVariable;
                array_shift($variables);
            }

            $this->parameterValueList = $this->parseModelNameFromUri($variables);
        }
    }

    /**
     * @return string
     */
    public function render(): string{
        $error = $this->model->preRender();
        if ($error !== null){
            return $error->toJson();
        }

        /** @var responseInterface $apiResponse */
        $apiResponse = $this->model->{$this->verb}();

        $code = $apiResponse->getStatus();
        $GLOBALS['http_response_code'] = $code;

        header(dataResponse::generateProtocol() . ' ' . $code . ' ' . $apiResponse->generateText());

        return $apiResponse->toJson();
    }
}