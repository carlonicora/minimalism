<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Interfaces\SimpleObjectInterface;

class SimpleObject1Stub implements SimpleObjectInterface
{
    public function __construct(private ?string $name = null)
    {
    }
}