<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\JsonApi\Document;

interface TransformerInterface extends ServiceInterface
{
    /**
     * @param Document $document
     * @param string $viewFile
     * @return string
     */
    public function transform(Document $document, string $viewFile): string;

    /**
     * @return string
     */
    public function getContentType(): string;
}