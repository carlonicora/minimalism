<?php
namespace CarloNicora\Minimalism\Commands;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\DataLoaderInterface;
use CarloNicora\Minimalism\Interfaces\EncryptedParameterInterface;
use CarloNicora\Minimalism\Interfaces\ParameterInterface;
use CarloNicora\Minimalism\Interfaces\PositionedParameterInterface;
use CarloNicora\Minimalism\Interfaces\ResourceLoaderInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Parameters\EncryptedParameter;
use CarloNicora\Minimalism\Parameters\PositionedEncryptedParameter;
use CarloNicora\Minimalism\Parameters\PositionedParameter;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

class FunctionParametersCommand
{
    /**
     * FunctionParametersCommand constructor.
     * @param ServiceFactory $services
     */
    public function __construct(
        private ServiceFactory $services,
    ) {}

    /**
     * @param string $modelClass
     * @param string $functionName
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    public function generateFunctionParameters(
        string $modelClass,
        string $functionName,
        array $parameters,
    ): array
    {
        $response = [];

        $method = new ReflectionMethod(
            $modelClass,
            $functionName
        );
        $methodParameters = $method->getParameters();

        /** @var ReflectionParameter $methodParameter */
        foreach ($methodParameters ?? [] as $methodParameter) {
            $newParameter = null;
            $newParameterClass = null;

            /** @var ReflectionNamedType $parameter */
            $parameter = $methodParameter->getType();
            try {
                $methodParameterType = new ReflectionClass($parameter->getName());

                if ($methodParameterType->implementsInterface(ServiceInterface::class)) {
                    $response[] = $this->getServiceInterfaceParameter($parameter);
                } elseif ($methodParameterType->implementsInterface(EncryptedParameterInterface::class)) {
                    $response[] = $this->getEncryptedParameter(
                        $methodParameterType,
                        $methodParameter,
                        $parameters,
                    );
                } elseif ($methodParameterType->implementsInterface(ParameterInterface::class)) {
                    $response[] = $this->getParameter(
                        methodParameterType: $methodParameterType,
                        methodParameter: $methodParameter,
                        parameter: $parameter,
                        parameters: $parameters
                    );
                } elseif ($methodParameterType->implementsInterface(DataLoaderInterface::class) || $methodParameterType->implementsInterface(ResourceLoaderInterface::class)){
                    if (($pools = $this->services->create('CarloNicora\\Minimalism\\Services\\Pools\\Pools')) === null){
                        throw new RuntimeException('The system has not required minimalism-service-pools', 500);
                    }

                    /** @noinspection PhpUndefinedMethodInspection */
                    $response[] = $pools->get($parameter->getName());
                } elseif (
                    $parameter->getName() === Document::class
                    && array_key_exists($methodParameter->getName(), $parameters['named'])
                    && is_array($parameters['named'][$methodParameter->getName()])
                ) {
                    $response[] = $this->getDocumentParameter(
                        methodParameter: $methodParameter,
                        parameters: $parameters,
                    );
                }
            } catch (ReflectionException) {
                $response[] = $this->getSimpleParameter(
                    methodParameter: $methodParameter,
                    parameter: $parameter,
                    parameters: $parameters,
                );
            }
        }

        return $response;
    }

    /**
     * @param ReflectionParameter $methodParameter
     * @param ReflectionNamedType $parameter
     * @param array $parameters
     * @return mixed
     * @throws Exception
     */
    private function getSimpleParameter(
        ReflectionParameter $methodParameter,
        ReflectionNamedType $parameter,
        array $parameters,
    ): mixed
    {
        if (!array_key_exists($methodParameter->getName(), $parameters['named']) && !$parameter->allowsNull()){
            throw new RuntimeException('Required parameter missing: ' . $methodParameter->getName(), 412);
        }

        if (array_key_exists('named', $parameters) && array_key_exists($methodParameter->getName(), $parameters['named'])){
            return $parameters['named'][$methodParameter->getName()];
        }

        return $methodParameter->isDefaultValueAvailable() ? $methodParameter->getDefaultValue() : null;
    }

    /**
     * @param ReflectionParameter $methodParameter
     * @param array $parameters
     * @return Document
     * @throws Exception
     */
    private function getDocumentParameter(
        ReflectionParameter $methodParameter,
        array $parameters,
    ): Document
    {
        return new Document(
            $parameters['named'][$methodParameter->getName()]
        );
    }

    /**
     * @param ReflectionNamedType $parameter
     * @return ServiceInterface
     * @throws Exception
     */
    private function getServiceInterfaceParameter(
        ReflectionNamedType $parameter
    ): ServiceInterface
    {
        $classResponse = $this->services->create($parameter->getName());
        if ($classResponse === null && !$parameter->allowsNull()){
            throw new RuntimeException($parameter->getName() . ' missing', 500);
        }

        return $classResponse;
    }

    /**
     * @param ReflectionClass $methodParameterType
     * @param ReflectionParameter $methodParameter
     * @param array $parameters
     * @return PositionedParameterInterface|ParameterInterface|null
     * @throws Exception
     */
    private function getEncryptedParameter(
        ReflectionClass $methodParameterType,
        ReflectionParameter $methodParameter,
        array &$parameters,
    ): PositionedParameterInterface|ParameterInterface|null
    {
        $newParameter = null;

        if ($methodParameterType->implementsInterface(PositionedParameterInterface::class)) {
            $newParameterClass = PositionedEncryptedParameter::class;
            if (array_key_exists('positioned', $parameters) && array_key_exists(0, $parameters['positioned'])) {
                $newParameter = array_shift($parameters['positioned']);
            }
        } else {
            $newParameterClass = EncryptedParameter::class;
            /** @noinspection NotOptimalIfConditionsInspection */
            if (array_key_exists($methodParameter->getName(), $parameters['named'])){
                $newParameter = $parameters['named'][$methodParameter->getName()];
            }
        }

        if ($newParameter !== null) {
            $parameterClass = new $newParameterClass($newParameter);
            if ($this->services->getEncrypter() !== null) {
                /** @var PositionedEncryptedParameter $parameterClass */
                $parameterClass->setEncrypter($this->services->getEncrypter());
            } else {
                throw new RuntimeException('No encrypter has been specified', 500);
            }

            return $parameterClass;
        }

        return null;
    }

    /**
     * @param ReflectionClass $methodParameterType
     * @param ReflectionParameter $methodParameter
     * @param ReflectionNamedType $parameter
     * @param array $parameters
     * @return ParameterInterface|null
     * @throws Exception
     */
    private function getParameter(
        ReflectionClass $methodParameterType,
        ReflectionParameter $methodParameter,
        ReflectionNamedType $parameter,
        array &$parameters,
    ): ParameterInterface|null
    {
        $newParameter = null;

        if ($methodParameterType->implementsInterface(PositionedParameterInterface::class)) {
            $newParameterClass = PositionedParameter::class;
            if (array_key_exists('positioned', $parameters) && array_key_exists(0, $parameters['positioned'])) {
                $newParameter = array_shift($parameters['positioned']);
            }
        } else {
            $newParameterClass = $methodParameterType->getName();
            if (array_key_exists('named', $parameters) && array_key_exists($parameter->getName(), $parameters['named'])){
                $newParameter = $parameters['named'][$parameter->getName()];
            }
        }

        if ($newParameter === null && !$parameter->allowsNull()){
            throw new RuntimeException('Required parameter missing: ' . $methodParameter->getName(), 412);
        }

        if ($newParameter !== null) {
            return new $newParameterClass($newParameter);
        }

        return $methodParameter->isDefaultValueAvailable() ? $methodParameter->getDefaultValue() : null;
    }
}