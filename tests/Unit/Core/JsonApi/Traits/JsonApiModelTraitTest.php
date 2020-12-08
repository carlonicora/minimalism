<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\JsonApi\Traits;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\JsonApi\Traits\JsonApiModelTrait;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;

class JsonApiModelTraitTest extends AbstractTestCase
{

    public function testGenerateResponse()
    {
        $mock = $this->getMockForTrait(JsonApiModelTrait::class);

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $mock->generateResponse(new Document(), ResponseInterface::HTTP_STATUS_200);
        self::assertEquals('application/vnd.api+json', $response->getContentType());
        self::assertEquals(ResponseInterface::HTTP_STATUS_200, $response->getStatus());

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $mock->generateResponse(new Document(), ResponseInterface::HTTP_STATUS_404);
        self::assertEquals(ResponseInterface::HTTP_STATUS_404, $response->getStatus());
    }

    public function testGenerateResponseFromError()
    {
        $mock = $this->getMockForTrait(JsonApiModelTrait::class);

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $mock->generateResponseFromError(new Exception('Test exception'));
        self::assertEquals('500', $response->getStatus());

        /** @noinspection PhpUndefinedMethodInspection */
        $response = $mock->generateResponseFromError(new Exception('Test exception', 501));
        self::assertEquals('501', $response->getStatus());
    }
}
