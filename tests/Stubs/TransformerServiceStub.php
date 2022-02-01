<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;

class TransformerServiceStub implements TransformerInterface
{

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
        return 'baseTransformerInterface';
    }

    public function transform(Document $document, string $viewFile,): string
    {
        return '';
    }

    public function getContentType(): string
    {
        return '';
    }
}