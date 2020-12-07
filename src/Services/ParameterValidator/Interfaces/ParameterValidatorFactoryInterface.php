<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Interfaces;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;

interface ParameterValidatorFactoryInterface
{
    /**
     * @param ServicesFactory $services
     * @param string $parameterValidatorClass
     * @return ParameterValidatorInterface
     */
    public function createParameterValidator(ServicesFactory $services, string $parameterValidatorClass) : ParameterValidatorInterface;
}