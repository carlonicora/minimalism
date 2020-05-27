<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Interfaces;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;

interface ParameterValidatorFactoryInterface
{
    /**
     * @param ServicesFactory $services
     * @param ParameterObject $parameter
     * @return ParameterValidatorInterface
     */
    public function createParameterValidator(ServicesFactory $services, ParameterObject $parameter) : ParameterValidatorInterface;
}