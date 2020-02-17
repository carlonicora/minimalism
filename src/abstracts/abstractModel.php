<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\helpers\errorReporter;
use carlonicora\minimalism\helpers\idEncrypter;

abstract class abstractModel {
    /** @var abstractConfigurations */
    protected $configurations;

    /** @var array */
    protected $parameterValues;

    /** @var array */
    protected $parameterValueList;

    /** @var array */
    protected $file;

    /** @var string */
    public $redirectPage;

    /** @var array */
    protected $parameters;

    /** @var array */
    protected $encryptedParameters;

    /**
     * model constructor.
     * @param abstractConfigurations $configurations
     * @param array $parameterValues
     * @param array $parameterValueList
     * @param array $file
     */
    public function __construct($configurations, $parameterValues, $parameterValueList, $file=null){
        $this->configurations = $configurations;
        $this->parameterValues = $parameterValues;
        $this->parameterValueList = $parameterValueList;
        $this->file = $file;

        $this->buildParameters();

        $this->redirectPage = null;
    }

    /**
     *
     */
    private function buildParameters(): void{
        $idEncrypter = new idEncrypter($this->configurations);

        if ($this->parameters !== null) {
            $parameters = $this->getParameters();

            foreach ($parameters ?? [] as $parameterName=>$isParameterRequired){
                if (array_key_exists($parameterName, $this->parameterValues)) {
                    $this->$parameterName = $this->parameterValues[$parameterName];
                } else if (array_key_exists(($parameterName), $this->parameterValueList)){
                    $this->$parameterName = $this->parameterValueList[($parameterName)];
                } else if ($isParameterRequired){
                    errorReporter::returnHttpCode(412,'Required parameter' . $parameterName . ' missing.');
                    exit;
                }

                if (!empty($this->$parameterName) && array_key_exists($parameterName, $this->encryptedParameters)){
                    $this->$parameterName = $idEncrypter->decryptId($this->$parameterName);
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getParameters(): array {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function redirect(): string {
        return $this->redirectPage ?? '';
    }
}