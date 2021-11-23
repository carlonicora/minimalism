<?php
namespace CarloNicora\Minimalism\Enums;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Objects\ParameterDefinition;
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

    /**
     * @param ParameterDefinition $parameterDefinition
     * @param MinimalismFactories $minimalismFactories
     * @param array $parameters
     * @return mixed
     * @throws Exception
     */
    public function getParameterValue(
        ParameterDefinition $parameterDefinition,
        MinimalismFactories $minimalismFactories,
        array $parameters,
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
                if (array_key_exists('positioned', $parameters) && array_key_exists(0, $parameters['positioned'])) {
                    $response = array_shift($parameters['positioned']);
                }
                break;
            case self::Parameter:
                if (array_key_exists('named', $parameters) && array_key_exists($parameterDefinition->getName(), $parameters['named'])){
                    $response = $parameters['named'][$parameterDefinition->getName()];
                }
                break;
            case self::Document:
            case self::Simple:
                $response = null;
                if (array_key_exists('named', $parameters) && array_key_exists($parameterDefinition->getName(), $parameters['named'])){
                    $parameterValue = $parameters['named'][$parameterDefinition->getName()];
                } elseif (array_key_exists($parameterDefinition->getName(), $parameters)){
                    $parameterValue = $parameters[$parameterDefinition->getName()];
                } else {
                    $parameterValue = null;
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
                    parameters: $parameters,
                );
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