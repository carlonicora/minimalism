<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Commands\DecrypterCommand;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\DecrypterInterface;
use Exception;

abstract class AbstractModel implements ModelInterface
{
    /** @var ServicesFactory */
    protected ServicesFactory $services;

    /** @var array */
    protected ?array $file;

    /** @var ModelInterface|string|null */
    protected $redirectPage;

    /** @var array */
    protected array $parameters=[];

    /** @var array  */
    protected array $passedParameters = [];

    /** @var array  */
    protected array $receivedParameters = [];

    /** @var EncrypterInterface=null */
    protected ?EncrypterInterface $encrypter=null;

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
     * @param string $verb
     */
    public function setVerb(string $verb) : void
    {
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return ModelInterface|string
     */
    public function redirect()
    {
        return $this->redirectPage ?? '';
    }

    /**
     * @param Exception $e
     * @return Response
     */
    public function getResponseFromError(Exception $e): ResponseInterface
    {
        $response = new Response();
        $response->setStatus((string)$e->getCode());
        $response->setData($e->getMessage());

        return $response;
    }

    /**
     *
     */
    abstract public function preRender() : void;

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
        return new DecrypterCommand($this->encrypter);
    }

    /**
     * @param EncrypterInterface|null $encrypter
     */
    public function setEncrypter(?EncrypterInterface $encrypter): void
    {
        $this->encrypter = $encrypter;
    }
}
