<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\DefaultServiceInterface;

class DefaultServiceStub implements DefaultServiceInterface
{

    public function getDelayedServices(): array
    {
        return [];
    }

    public function getApplicationUrl(): ?string
    {
        return '/';
    }

    public function getApiUrl(): ?string
    {
        return '/';
    }

    public function initialise(): void
    {
        // TODO: Implement initialise() method.
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
        // TODO: Implement setObjectFactory() method.
    }

    public function unsetObjectFactory(): void
    {
        // TODO: Implement unsetObjectFactory() method.
    }

    public static function getBaseInterface(): ?string
    {
        return 'baseDefaultInterface';
    }
}