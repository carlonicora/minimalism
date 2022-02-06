<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ModelFactory;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;

class ComplexArgumentsServiceStub implements ServiceInterface
{
    public function __construct(
        public TransformerServiceStub|DefaultServiceStub $defaultService,
        public MinimalismFactories $minimalismFactories,
        public ServiceFactory $serviceFactory,
        public ObjectFactory $objectFactory,
        public ModelFactory $modelFactory,
        public ServiceStub $serviceStub
    )
    {
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
        return '';
    }
}