<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\JsonApi\Traits;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\JsonApi\Traits\JsonApiModelTrait;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class JsonApiModelTraitTest extends AbstractTestCase
{

    public function testGenerateResponse()
    {
        $mock = $this->getMockForTrait(JsonApiModelTrait::class);

        $response = $mock->generateResponse(new Document(), ResponseInterface::HTTP_STATUS_200);
        $this->assertEquals('application/vnd.api+json', $response->getContentType());
        $this->assertEquals(ResponseInterface::HTTP_STATUS_200, $response->getStatus());

        $response = $mock->generateResponse(new Document(), ResponseInterface::HTTP_STATUS_404);
        $this->assertEquals(ResponseInterface::HTTP_STATUS_404, $response->getStatus());
    }

    public function testGenerateResponseFromError()
    {
        $mock = $this->getMockForTrait(JsonApiModelTrait::class);

        $response = $mock->generateResponseFromError(new \Exception('Test exception'));
        $this->assertEquals('0', $response->getStatus());

        $response = $mock->generateResponseFromError(new \Exception('Test exception', 501));
        $this->assertEquals('501', $response->getStatus());
    }
}
