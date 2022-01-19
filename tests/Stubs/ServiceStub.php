<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;

class ServiceStub implements ServiceInterface
{

    public function initialise(): void
    {
        // TODO: Implement initialise() method.
    }

    public function destroy(): void
    {
        // TODO: Implement destroy() method.
    }

    public function setObjectFactory(ObjectFactory $objectFactory): void
    {
        // TODO: Implement setObjectFactory() method.
    }

    public static function getBaseInterface(): ?string
    {
        return '';
    }
}