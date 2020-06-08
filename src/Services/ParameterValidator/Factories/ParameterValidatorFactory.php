<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Factories;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\ParameterValidatorFactoryInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\ParameterValidatorInterface;
use Exception;
use ReflectionClass;
use ReflectionException;

class ParameterValidatorFactory implements ParameterValidatorFactoryInterface
{
    /**
     * @param ServicesFactory $services
     * @param string $parameterValidatorClass
     * @return ParameterValidatorInterface
     * @throws Exception
     */
    public function createParameterValidator(ServicesFactory $services, string $parameterValidatorClass) : ParameterValidatorInterface
    {
        /** @var ParameterValidatorInterface $response */
        try {
            $reflector = new ReflectionClass($parameterValidatorClass);
            $response = $reflector->newInstanceArgs([$services]);
        } catch (ReflectionException $e) {
            $services->logger()->error()->log(
                MinimalismErrorEvents::PARAMETER_VALIDATOR_ERROR($parameterValidatorClass, $e)
            )->throw(ConfigurationException::class, 'Parameter Validator not found');
        }

        return $response;
    }
}
