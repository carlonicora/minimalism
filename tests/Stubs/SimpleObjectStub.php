<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Interfaces\SimpleObjectInterface;

class SimpleObjectStub implements SimpleObjectInterface
{
    public function __construct(private ?string $name = null)
    {
    }
}