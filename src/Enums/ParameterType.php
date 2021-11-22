<?php
namespace CarloNicora\Minimalism\Enums;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ObjectFactoryInterface;
use CarloNicora\Minimalism\Objects\ParameterDefinition;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionUnionType;
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

    /**
     * @param ParameterDefinition $parameterDefinition
     * @param ServiceFactory $serviceFactory
     * @param array $parameters
     * @return mixed
     * @throws Exception
     */
    public function getParameterValue(
        ParameterDefinition $parameterDefinition,
        ServiceFactory $serviceFactory,
        array $parameters,
    ): mixed
    {
        $response = null;

        switch ($this){
            case self::Null:
                throw new RuntimeException('', 500);
            case self::Service:
                $response = $serviceFactory->create(
                    className: $parameterDefinition->getIdentifier(),
                );

                if (is_string($response)){
                    $response = $serviceFactory->create(
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
                $factoryName = null;

                try {
                    /** @var ReflectionUnionType $types */
                    $types = (new ReflectionClass($parameterDefinition->getIdentifier()))->getMethod('getObjectFactoryClass')->getReturnType();

                    foreach ($types->getTypes() as $type){
                        if ($type->getName() !== 'string'){
                            $factoryName = $type->getName();
                            break;
                        }
                    }
                } catch (ReflectionException) {
                    $factoryName = null;
                }

                if ($factoryName === null){
                    throw new RuntimeException('nope', 500);
                }

                try {
                    /** @var ObjectFactoryInterface $factory */
                    $factory = new $factoryName();

                    $response = $factory->create(
                        $parameterDefinition->getIdentifier(),
                        $parameters,
                    );
                } catch (Exception) {
                    throw new RuntimeException('nope', 500);
                }

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