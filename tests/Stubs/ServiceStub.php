<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;

class ServiceStub implements ServiceInterface
{

    private ?ObjectFactory $objectFactory = null;

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
        $this->objectFactory = $objectFactory;
    }

    public static function getBaseInterface(): ?string
    {
        return '';
    }

    public function postIntialise(ServiceFactory $services,): void
    {
        // TODO: Implement postIntialise() method.
    }

    public function unsetObjectFactory(): void
    {
        // TODO: Implement unsetObjectFactory() method.
    }
}