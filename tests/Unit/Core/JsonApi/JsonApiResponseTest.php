<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\JsonApi;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use JsonException;

class JsonApiResponseTest extends AbstractTestCase
{

    public function testGetDocumentWithDefaultAndProvidedEmptyDocument()
    {
        $instance = new JsonApiResponse();
        $this->assertInstanceOf(Document::class, $instance->getDocument());

        $document = new Document();
        $instance->setDocument($document);
        $this->assertSame($document, $instance->getDocument());
    }


    public function testGetDataWithDefaultAndProvidedEmptyDocument()
    {
        $instance = new JsonApiResponse();
        $this->assertEmpty($instance->getData());

        $document = new Document();
        $instance->setDocument($document);
        $this->assertEquals('{"meta":[]}', $instance->getData());


        $mock = $this->getMockBuilder(Document::class)
            ->getMock();

        $mock->expects($this->once())->method('export')->willThrowException(new JsonException());

        /** @noinspection PhpParamsInspection */
        $instance->setDocument($mock);
        $this->assertEquals('', $instance->getData());
    }
}
