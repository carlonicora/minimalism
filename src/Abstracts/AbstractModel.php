<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Interfaces\ParameterInterface;
use CarloNicora\Minimalism\Interfaces\PositionedParameterInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Objects\PositionedParameter;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

class AbstractModel implements ModelInterface
{
    /** @var string  */
    private string $function;

    /** @var string|null  */
    private ?string $view=null;

    /** @var array  */
    private array $parameters=[];

    /** @var Document  */
    protected Document $document;

    /**
     * AbstractModel constructor.
     * @param ServiceFactory $services
     */
    public function __construct(private ServiceFactory $services)
    {
        if ($this->services->getUrl() === null) {
            $this->function = 'cli';
        } else {
            $this->function = strtolower($_SERVER['REQUEST_METHOD'] ?? 'GET');
            if ($this->function === 'post' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'DELETE') {
                    $this->function = 'delete';
                } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PUT') {
                    $this->function = 'put';
                }
            }
        }

        $this->document = new Document();
    }

    /**
     * @param array $parameters
     */
    final public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return Document
     */
    final public function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * @return string|null
     */
    final public function getView(): ?string
    {
        return $this->view;
    }

    /**
     * @return int
     * @throws Exception
     */
    final public function run(): int
    {
        $parameters = [];
        $method = new ReflectionMethod(get_class($this), $this->function);
        $methodParameters = $method->getParameters();

        foreach ($methodParameters ?? [] as $methodParameter) {
            $newParameter = null;
            $newParameterClass = null;

            /** @var ReflectionNamedType $parameter */
            $parameter = $methodParameter->getType();
            try {
                $methodParameterType = new ReflectionClass($parameter->getName());
                if ($methodParameterType->implementsInterface(ServiceInterface::class)) {
                    $parameters[] = $this->services->create($parameter->getName());
                } elseif ($methodParameterType->implementsInterface(ParameterInterface::class)){
                    if ($methodParameterType->implementsInterface(PositionedParameterInterface::class)){
                        $newParameterClass = PositionedParameter::class;
                        if (array_key_exists('positioned', $this->parameters) && array_key_exists(0, $this->parameters['positioned'])){
                            $newParameter = array_shift($this->parameters['positioned']);
                        }
                    } else {
                        $newParameterClass = $methodParameterType->getName();
                        if (array_key_exists('named', $this->parameters) && array_key_exists($parameter->getName(), $this->parameters['named'])){
                            $newParameter = $this->parameters['named'][$parameter->getName()];
                        }
                    }

                    if ($newParameter === null && !$parameter->allowsNull()){
                        throw new RuntimeException('Required parameter missing: ' . $methodParameter->getName(), 412);
                    }

                    $parameters[] = new $newParameterClass($newParameter);
                }
            } catch (ReflectionException) {
                if (!array_key_exists($methodParameter->getName(), $this->parameters['named']) && !$parameter->allowsNull()){
                    throw new RuntimeException('Required parameter missing: ' . $methodParameter->getName(), 412);
                }

                if (array_key_exists('named', $this->parameters) && array_key_exists($methodParameter->getName(), $this->parameters['named'])){
                    $parameters[] = $this->parameters['named'][$methodParameter->getName()];
                } else {
                    $parameters[] = null;
                }
            }
        }

        return $this->{$this->function}(...$parameters);
    }
}