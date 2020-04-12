<?php
namespace carlonicora\minimalism\core\modules\abstracts\models;

use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use Exception;
use RuntimeException;

abstract class abstractModel {
    /** @var servicesFactory */
    protected servicesFactory $services;

    /** @var array */
    protected ?array $file;

    /** @var string */
    public ?string $redirectPage;

    /** @var array */
    protected array $parameters=[];

    /**
     * model constructor.
     * @param servicesFactory $services
     * @param array $passedParameters
     * @param array $file
     * @throws serviceNotFoundException
     */
    public function __construct(servicesFactory $services, array $passedParameters, array $file=null){
        $this->services = $services;
        $this->file = $file;

        $this->buildParameters($passedParameters);

        $this->redirectPage = null;
    }

    /**
     * @param array $passedParameters
     * @throws serviceNotFoundException
     * @throws Exception
     */
    private function buildParameters(array $passedParameters): void{
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

                if (array_key_exists($parameterKey, $passedParameters) && $passedParameters[$parameterKey] !== null) {
                    if (($isParameterEncrypted || (!empty($this->encryptedParameters) && in_array($parameterName, $this->encryptedParameters, true))) && class_exists('carlonicora\minimalism\services\encrypter\encrypter')){
                        $this->$parameterName = $this->services->service('carlonicora\minimalism\services\encrypter\encrypter')->decryptId($passedParameters[$parameterKey]);
                    } else {
                        $this->$parameterName = $passedParameters[$parameterKey];
                    }
                } elseif ($isParameterRequired){
                    throw new RuntimeException('Required parameter ' . $parameterName . ' missing.', 412);
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
     *
     */
    abstract public function preRender() : void;
}