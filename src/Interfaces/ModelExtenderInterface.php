<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Enums\HttpCode;

interface ModelExtenderInterface
{
    /**
     * @return string
     */
    public function getExtendedModel(
    ): string;

    /**
     * @param Document $document
     * @param HttpCode $response
     * @return void
     */
    public function setExtendedModelResult(
        Document $document,
        HttpCode $response,
    ): void;
}