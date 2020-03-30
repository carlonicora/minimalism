<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\factories\encrypterFactory;
use carlonicora\minimalism\jsonapi\responses\dataResponse;
use carlonicora\minimalism\jsonapi\responses\errorResponse;

abstract class abstractModel {
    /** @var abstractConfigurations */
    protected abstractConfigurations $configurations;

    /** @var array */
    protected array $parameterValues;

    /** @var array */
    protected array $parameterValueList;

    /** @var array */
    protected ?array $file;

    /** @var string */
    public ?string $redirectPage;

    /** @var array */
    protected array $parameters=[];

    /** @var array */
    protected array $encryptedParameters=[];

    /** @var dataResponse  */
    protected dataResponse $response;

    /** @var errorResponse|null  */
    protected ?errorResponse $error=null;

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

        $this->response = new dataResponse();
    }

    /**
     *
     */
    private function buildParameters(): void{
        if ($this->parameters !== null) {
            $parameters = $this->getParameters();

            foreach ($parameters ?? [] as $parameterKey=>$value) {
                $parameterName = $value;
                $isParameterRequired = false;
                $isParameterEncrypted = false;

                if (is_array($value)) {
                    $parameterName = $value['name'];
                    $isParameterRequired = $value['required'] ?? false;
                    $isParameterEncrypted = $value['encrypted'] ?? false;
                }

                if (array_key_exists($parameterKey, $this->parameterValues) && $this->parameterValues[$parameterKey] !== null) {
                    if ($isParameterEncrypted || (!empty($this->encryptedParameters) && in_array($parameterName, $this->encryptedParameters, true))){
                        $this->$parameterName = encrypterFactory::encrypter()->decryptId($this->parameterValues[$parameterKey]);
                    } else {
                        $this->$parameterName = $this->parameterValues[$parameterKey];
                    }
                } else if (array_key_exists($parameterKey, $this->parameterValueList) && $this->parameterValueList[$parameterKey] !== null){
                    if ($isParameterEncrypted || (!empty($this->encryptedParameters) && in_array($parameterName, $this->encryptedParameters, true))){
                        $this->$parameterName = encrypterFactory::encrypter()->decryptId($this->parameterValueList[$parameterKey]);
                    } else {
                        $this->$parameterName = $this->parameterValueList[$parameterKey];
                    }
                } else if ($isParameterRequired){
                    $this->error = new errorResponse(errorResponse::HTTP_STATUS_412, 'Required parameter' . $parameterName . ' missing.');
                    break;
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

    /**
     * @return errorResponse|null
     */
    public function preRender() : ?errorResponse {
        return $this->error;
    }
}