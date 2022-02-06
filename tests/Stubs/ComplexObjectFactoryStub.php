<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Interfaces\ObjectFactoryInterface;
use CarloNicora\Minimalism\Interfaces\ObjectInterface;
use CarloNicora\Minimalism\Objects\ModelParameters;

class ComplexObjectFactoryStub implements ObjectFactoryInterface
{
    public function __construct()
    {
    }

    public function create(string $className, string $parameterName, ModelParameters $parameters,): ?ObjectInterface
    {
        return new ComplexObjectStub();
    }
}