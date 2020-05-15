<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Factories;

use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\ParameterValidatorFactoryInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\ParameterValidatorInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use ReflectionClass;
use ReflectionException;

class ParameterValidatorFactory implements ParameterValidatorFactoryInterface
{
    /**
     * @param ParameterObject $parameter
     * @return ParameterValidatorInterface
     * @throws ConfigurationException
     */
    public function createParameterValidator(ParameterObject $parameter) : ParameterValidatorInterface
    {
        /** @var ParameterValidatorInterface $response */
        try {
            $reflector = new ReflectionClass($parameter->validator);
            $response = $reflector->newInstanceArgs([$parameter]);
        } catch (ReflectionException $e) {
            throw new ConfigurationException('minimalism', 'Parameter Validator not foud', ConfigurationException::PARAMETER_VALIDATOR_NOT_FOUND);
        }

        return $response;
    }
}