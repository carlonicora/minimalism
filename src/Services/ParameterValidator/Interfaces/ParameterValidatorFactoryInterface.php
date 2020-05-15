<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Interfaces;

use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;

interface ParameterValidatorFactoryInterface
{
    /**
     * @param ParameterObject $parameter
     * @return ParameterValidatorInterface
     */
    public function createParameterValidator(ParameterObject $parameter) : ParameterValidatorInterface;
}