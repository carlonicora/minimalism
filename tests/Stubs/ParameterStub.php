<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Interfaces\ParameterInterface;

class ParameterStub implements ParameterInterface
{
    public function __construct(mixed $value)
    {
    }

    public function getValue(): mixed
    {
        return '';
    }
}