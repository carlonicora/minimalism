<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Factories;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\ParameterValidatorFactoryInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\ParameterValidatorInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use Exception;
use ReflectionClass;
use ReflectionException;

class ParameterValidatorFactory implements ParameterValidatorFactoryInterface
{
    /**
     * @param ServicesFactory $services
     * @param ParameterObject $parameter
     * @return ParameterValidatorInterface
     * @throws Exception|ConfigurationException
     */
    public function createParameterValidator(ServicesFactory $services, ParameterObject $parameter) : ParameterValidatorInterface
    {
        /** @var ParameterValidatorInterface $response */
        try {
            $reflector = new ReflectionClass($parameter->validator);
            $response = $reflector->newInstanceArgs([$services, $parameter]);
        } catch (ReflectionException $e) {
            $services->logger()->error()->log(
                MinimalismErrorEvents::PARAMETER_VALIDATOR_ERROR($parameter->validator, $e)
            )->throw(ConfigurationException::class, 'Parameter Validator not foud');
        }

        return $response;
    }
}