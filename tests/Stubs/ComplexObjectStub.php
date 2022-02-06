<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Interfaces\ObjectFactoryInterface;
use CarloNicora\Minimalism\Interfaces\ObjectInterface;

class ComplexObjectStub implements ObjectInterface
{
    public function getObjectFactoryClass(): ComplexObjectFactoryStub|string
    {
        return ComplexObjectFactoryStub::class;
    }
}