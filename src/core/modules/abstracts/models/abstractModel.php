<?php
namespace carlonicora\minimalism\core\modules\abstracts\models;

use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\logger\logger;
use DateTime;
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

    /** @var logger  */
    protected logger $logger;

    /** @var array  */
    protected array $passedParameters = [];

    public const PARAMETER_TYPE_INT = 'validateIntParameter';
    public const PARAMETER_TYPE_STRING = 'validateStringParameter';
    public const PARAMETER_TYPE_BOOL = 'validateBoolParameter';
    public const PARAMETER_TYPE_TIMESTAMP = 'validateTimestampParameter';
    public const PARAMETER_TYPE_DATETIME = 'validateDatetimeParameter';
    public const PARAMETER_TYPE_DECIMAL = 'validateDecimalParameter';

    /**
     * model constructor.
     * @param servicesFactory $services
     * @param array $passedParameters
     * @param array $file
     * @throws serviceNotFoundException|Exception
     */
    public function __construct(servicesFactory $services, array $passedParameters, array $file=null){
        $this->services = $services;

        $this->logger = $services->service(logger::class);

        $this->file = $file;

        $this->buildParameters($passedParameters);

        $this->logger->addSystemEvent(null, 'Model parameters built');

        $this->redirectPage = null;
    }

    /**
     * @param array $passedParameters
     * @throws Exception
     */
    private function buildParameters(array $passedParameters): void{
        if ($this->parameters !== null) {
            $parameters = $this->getParameters();

            foreach ($parameters ?? [] as $parameterKey=>$value) {
                $parameterName = $value;
                $isParameterRequired = false;
                $isParameterEncrypted = false;

                $parameterValidation = $value['validation'] ?? self::PARAMETER_TYPE_STRING;

                if (is_array($value)) {
                    $parameterName = $value['name'];
                    $isParameterRequired = $value['required'] ?? false;
                    $isParameterEncrypted = $value['encrypted'] ?? false;
                    $parameterValidation = $value['validation'] ?? self::PARAMETER_TYPE_STRING;
                }

                $this->passedParameters[] = $parameterKey;

                if (array_key_exists($parameterKey, $passedParameters)) {
                    if ($passedParameters[$parameterKey] !== null
                        && ($isParameterEncrypted || (!empty($this->encryptedParameters) && in_array($parameterName, $this->encryptedParameters, true)))
                    ) {
                        $this->$parameterName = $this->decryptParameter($passedParameters[$parameterKey]);
                    } elseif (method_exists($this, $parameterValidation)){
                        $this->$parameterName = $this->$parameterValidation($passedParameters[$parameterKey]);
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
     * @param $variable
     * @return string
     */
    protected function validateStringParameter($variable) : string {
        return (string)$variable;
    }

    /**
     * @param $variable
     * @return int
     */
    protected function validateIntParameter($variable) : int {
        return (int)$variable;
    }

    /**
     * @param $variable
     * @return float
     */
    protected function validateDecimalParameter($variable) : float {
        return (float)$variable;
    }

    /**
     * @param $parameter
     * @return bool
     */
    protected function validateBoolParameter($parameter) : bool {
        return (bool)$parameter;
    }

    /**
     * @param $parameter
     * @return DateTime
     * @throws Exception
     */
    protected function validateDatetimeParameter($parameter) : DateTime {
        if ((is_int($parameter))){
            return new DateTime('@' . $parameter);
        }

        if ((is_string($parameter))){
            return new DateTime($parameter);
        }

        throw new RuntimeException('no valid parameter');
    }

    /**
     * @param $parameter
     * @return int
     * @throws Exception
     */
    protected function validateTimestampParameter($parameter) : int {
        if ((is_int($parameter))) {
            return $parameter;
        }

        if ((is_string($parameter))) {
            $date = new DateTime($parameter);
            return $date->getTimestamp();
        }

        throw new RuntimeException('no valid parameter');
    }

    /**
     * @param string $parameter
     * @return string
     */
    protected function decryptParameter(string $parameter) : string {
        return $parameter;
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
    abstract public function preRender();

    /**
     * @param int|null $code
     * @param string|null $response
     * @return mixed
     */
    public function postRender(?int $code, ?string $response): void{}
}