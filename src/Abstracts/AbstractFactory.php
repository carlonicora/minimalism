<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Enums\ParameterType;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Interfaces\ObjectInterface;
use CarloNicora\Minimalism\Interfaces\ParameterInterface;
use CarloNicora\Minimalism\Interfaces\PositionedParameterInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\SimpleObjectInterface;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Objects\ParameterDefinition;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

abstract class AbstractFactory
{
    /**
     * @param MinimalismFactories $minimalismFactories
     */
    public function __construct(
        protected MinimalismFactories $minimalismFactories,
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

        foreach ($method->getParameters() as $methodParameter) {
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
                } elseif ($methodParameterType->getName() === MinimalismFactories::class){
                    $parameterDefinition->setType(ParameterType::MinimalismFactories);
                } elseif ($methodParameterType->getName() === ObjectFactory::class){
                    $parameterDefinition->setType(ParameterType::ObjectFactory);
                } elseif ($methodParameterType->implementsInterface(SimpleObjectInterface::class)) {
                    $parameterDefinition->setType(ParameterType::SimpleObject);
                    $parameterDefinition->setIdentifier($methodParameterType->getName());
                } elseif ($methodParameterType->implementsInterface(ObjectInterface::class)) {
                    $parameterDefinition->setType(ParameterType::Object);
                    $parameterDefinition->setIdentifier($methodParameterType->getName());
                } elseif ($methodParameterType->implementsInterface(PositionedParameterInterface::class)) {
                    $parameterDefinition->setType(ParameterType::PositionedParameter);
                } elseif ($methodParameterType->implementsInterface(ParameterInterface::class)) {
                    $parameterDefinition->setType(ParameterType::Parameter);
                } elseif ($methodParameterType->getName() === ModelParameters::class) {
                    $parameterDefinition->setType(ParameterType::ModelParameters);
                } elseif ($parameter->getName() === Document::class) {
                    $parameterDefinition->setType(ParameterType::Document);
                } elseif ($methodParameterType->isEnum()){
                    $parameterDefinition->setIdentifier($methodParameterType->getName());
                    $parameterDefinition->setType(ParameterType::Enum);
                } else {
                    throw new RuntimeException('Parameter type not supported', 500);
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
     * @param ParameterDefinition[] $methodParametersDefinition
     * @param ModelParameters|null $parameters
     * @return array
     * @throws Exception
     */
    public function generateMethodParametersValues(
        array $methodParametersDefinition,
        ?ModelParameters $parameters=null,
    ): array
    {
        $response = [];

        foreach ($methodParametersDefinition as $methodParameterDefinition) {
            $response[] = $methodParameterDefinition->getType()->getParameterValue(
                parameterDefinition: $methodParameterDefinition,
                minimalismFactories: $this->minimalismFactories,
                parameters: $parameters,
            );
        }

        return $response;
    }
}