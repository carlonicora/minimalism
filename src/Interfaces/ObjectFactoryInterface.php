<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\Minimalism\Objects\ModelParameters;

interface ObjectFactoryInterface
{
    /**
     * @param string $className
     * @param string $parameterName
     * @param ModelParameters $parameters
     * @return ObjectInterface|null
     */
    public function create(
        string $className,
        string $parameterName,
        ModelParameters $parameters,
    ): ?ObjectInterface;
}