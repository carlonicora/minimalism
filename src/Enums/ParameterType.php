<?php
namespace CarloNicora\Minimalism\Enums;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Objects\ParameterDefinition;
use CarloNicora\Minimalism\Parameters\PositionedParameter;
use Exception;
use RuntimeException;

enum ParameterType
{
    case Null;
    case Service;
    case Document;
    case Simple;
    case PositionedParameter;
    case Parameter;
    case Object;
    case SimpleObject;
    case MinimalismFactories;
    case ObjectFactory;
    case ModelParameters;

    /**
     * @param ParameterDefinition $parameterDefinition
     * @param MinimalismFactories $minimalismFactories
     * @param ModelParameters|null $parameters
     * @return mixed
     * @throws Exception
     */
    public function getParameterValue(
        ParameterDefinition $parameterDefinition,
        MinimalismFactories $minimalismFactories,
        ?ModelParameters $parameters=null,
    ): mixed
    {
        $response = null;

        switch ($this){
            case self::Null:
                throw new RuntimeException('', 500);
            case self::Service:
                $response = $minimalismFactories->getServiceFactory()->create(
                    className: $parameterDefinition->getIdentifier(),
                );

                if (is_string($response)){
                    $response = $minimalismFactories->getServiceFactory()->create(
                        className: $response,
                    );
                }
                break;
            case self::PositionedParameter:
                if (($parameterValue = $parameters?->getNextPositionedParameter()) !== null) {
                    $response = new PositionedParameter($parameterValue);
                }
                break;
            case self::Parameter:
                $response = $parameters?->getNamedParameter($parameterDefinition->getName());
                break;
            case self::Document:
            case self::Simple:
                if ($parameterDefinition->getName() === 'files'){
                    $parameterValue = $parameters?->getFiles();
                } else {
                    $parameterValue = $parameters?->getNamedParameter($parameterDefinition->getName());
                }

                if ($this === self::Document) {
                    if ($parameterValue !== null) {
                        $response = new Document($parameterValue);
                    }
                } else {
                    $response = $parameterValue;
                }
                break;
            case self::Object:
            case self::SimpleObject:
                $response = $minimalismFactories->getObjectFactory()->create(
                    className: $parameterDefinition->getIdentifier(),
                    name: $parameterDefinition->getName(),
                    parameters: $parameters,
                );
                break;
            case self::MinimalismFactories:
                $response = $minimalismFactories;
                break;
            case self::ObjectFactory:
                $response = $minimalismFactories->getObjectFactory();
                break;
            case self::ModelParameters:
                $response = $parameters;
                break;
        }

        if ($response === null && $parameterDefinition->getDefaultValue() !== null){
            $response = $parameterDefinition->getDefaultValue();
        }

        if ($response === null && !$parameterDefinition->allowsNull()){
            throw new RuntimeException(
                'Required parameter ' . $parameterDefinition->getName() . ' missing',
                412
            );
        }

        return $response;
    }
}