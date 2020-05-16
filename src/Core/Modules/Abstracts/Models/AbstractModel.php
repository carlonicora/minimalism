<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\DecrypterInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\DefaultDecrypter;
use Exception;

abstract class AbstractModel implements ModelInterface
{
    /** @var ServicesFactory */
    protected ServicesFactory $services;

    /** @var array */
    protected ?array $file;

    /** @var string */
    protected ?string $redirectPage;

    /** @var array */
    protected array $parameters=[];

    /** @var array  */
    protected array $passedParameters = [];

    /** @var array  */
    protected array $receivedParameters = [];

    public const PARAMETER_TYPE_INT = 'validateIntParameter';
    public const PARAMETER_TYPE_STRING = 'validateStringParameter';
    public const PARAMETER_TYPE_BOOL = 'validateBoolParameter';
    public const PARAMETER_TYPE_TIMESTAMP = 'validateTimestampParameter';
    public const PARAMETER_TYPE_DATETIME = 'validateDatetimeParameter';
    public const PARAMETER_TYPE_FLOAT = 'validateFloatParameter';

    /**
     * model constructor.
     * @param ServicesFactory $services
     */
    public function __construct(ServicesFactory $services)
    {
        $this->services = $services;
    }

    /**
     * @param array $passedParameters
     * @param array|null $file
     * @throws Exception
     */
    public function initialise(array $passedParameters, array $file=null) : void
    {
        $this->file = $file;

        $this->services->parameterValidator()->validate($this, $passedParameters);
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function redirect(): string
    {
        return $this->redirectPage ?? '';
    }

    /**
     *
     */
    abstract public function preRender() : void;

    /**
     * @param int $code
     * @param string $response
     * @return mixed
     */
    public function postRender(int $code, string $response): void
    {
    }

    /**
     * @param string $parameterName
     */
    public function addReceivedParameters(string $parameterName): void
    {
        $this->receivedParameters[] = $parameterName;
    }

    /**
     * @param string $parameterName
     * @param $parameterValue
     */
    public function setParameter(string $parameterName, $parameterValue): void
    {
        $this->$parameterName = $parameterValue;
    }

    /**
     * @return DecrypterInterface
     */
    public function decrypter(): DecrypterInterface
    {
        return new DefaultDecrypter();
    }
}