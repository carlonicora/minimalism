<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Enums\ParameterType;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ParameterInterface;
use CarloNicora\Minimalism\Interfaces\PositionedParameterInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Objects\ParameterDefinition;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

abstract class AbstractFactory
{
    /**
     * @param ServiceFactory $serviceFactory
     */
    public function __construct(
        protected ServiceFactory $serviceFactory,
    )
    {
    }

    /**
     * @param ReflectionMethod $method
     * @return ParameterDefinition[]
     */
    protected function getMethodParametersDefinition(
        ReflectionMethod $method
    ): array
    {
        $response = [];

        /** @var ReflectionParameter $methodParameter */
        foreach ($method->getParameters() ?? [] as $methodParameter) {
            $parameterDefinition = new ParameterDefinition(
                name: $methodParameter->getName(),
                allowsNull: $methodParameter->allowsNull(),
                defaultValue: $methodParameter->isDefaultValueAvailable() ? $methodParameter->getDefaultValue() : null,
            );

            /** @var ReflectionNamedType $parameter */
            $parameter = $methodParameter->getType();

            try {
                $methodParameterType = new ReflectionClass($parameter->getName());
                if ($methodParameterType->implementsInterface(ServiceInterface::class)) {
                    $parameterDefinition->setType(ParameterType::Service);
                    $parameterDefinition->setIdentifier($methodParameterType->getName());
                } elseif ($methodParameterType->implementsInterface(PositionedParameterInterface::class)) {
                    $parameterDefinition->setType(ParameterType::PositionedParameter);
                } elseif ($methodParameterType->implementsInterface(ParameterInterface::class)) {
                    $parameterDefinition->setType(ParameterType::Parameter);
                } elseif ($parameter->getName() === Document::class) {
                    $parameterDefinition->setType(ParameterType::Document);
                } else {
                    $parameterDefinition->setType(ParameterType::Object);
                    $parameterDefinition->setIdentifier($methodParameterType->getName());
                }
            } catch (ReflectionException) {
                $parameterDefinition->setType(ParameterType::Simple);
                $parameterDefinition->setIdentifier($parameter->getName());
            }

            $response[] = $parameterDefinition;
        }

        return $response;
    }

    /**
     * @param array $methodParametersDefinition
     * @param array|null $parameters
     * @return array
     */
    public function generateMethodParametersValues(
        array $methodParametersDefinition,
        ?array $parameters=[],
    ): array
    {
        $response = [];

        foreach ($methodParametersDefinition ?? [] as $methodParameterDefinition) {
            $response[] = $methodParameterDefinition->getType()->getParameterValue(
                parameterDefinition: $methodParameterDefinition,
                serviceFactory: $this->serviceFactory,
                parameters: $parameters,
            );
        }

        return $response;
    }

    /**
     * @param string $className
     * @param ParameterDefinition[] $methodParametersDefinition
     * @param array|null $parameters
     * @return array
     * @throws Exception
     */
    public function generateObject(
        string $className,
        array $methodParametersDefinition,
        ?array $parameters=[],
    ): mixed
    {
        $parametersValues = $this->generateMethodParametersValues($methodParametersDefinition, $parameters);
        return new $className(...$parametersValues);
    }
}