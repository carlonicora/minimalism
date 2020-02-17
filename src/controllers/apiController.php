<?php
namespace carlonicora\minimalism\controllers;

use carlonicora\minimalism\abstracts\abstractController;
use carlonicora\minimalism\helpers\errorReporter;
use carlonicora\minimalism\helpers\security;
use function carlonicora\minimalism\abstracts\getallheaders;

class apiController extends abstractController {
    /** @var string */
    private $signature;

    /**
     * apiController constructor.
     * @param $configurations
     * @param null $modelName
     * @param null $parameterValueList
     * @param null $parameterValues
     */
    public function __construct($configurations, $modelName = null, $parameterValueList = null, $parameterValues = null) {
        $this->initialiseVerb();

        $this->validateSignature();

        parent::__construct($configurations, $modelName, $parameterValueList, $parameterValues);
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