<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\JsonApi\Document;

interface TransformerInterface
{
    /**
     * @param Document $document
     * @param string $viewFile
     * @return string
     */
    public function transform(Document $document, string $viewFile): string;
}