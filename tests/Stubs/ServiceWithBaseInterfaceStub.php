<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;

class ServiceWithBaseInterfaceStub implements ServiceInterface
{
    private ?ObjectFactory $objectFactory = null;
    public bool $isInitialized = false;

    public function initialise(): void
    {
        $this->isInitialized = true;
    }

    public function postIntialise(ServiceFactory $services,): void
    {
        // TODO: Implement postIntialise() method.
    }

    public function destroy(): void
    {
        // TODO: Implement destroy() method.
    }

    public function setObjectFactory(ObjectFactory $objectFactory): void
    {
        $this->objectFactory = $objectFactory;
    }

    public function getObjectFactory(): ?ObjectFactory
    {
        return $this->objectFactory;
    }

    public function unsetObjectFactory(): void
    {
        // TODO: Implement unsetObjectFactory() method.
    }

    public static function getBaseInterface(): ?string
    {
        return InterfaceStub::class;
    }
}