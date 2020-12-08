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
        self::assertInstanceOf(Document::class, $instance->getDocument());

        $document = new Document();
        $instance->setDocument($document);
        self::assertSame($document, $instance->getDocument());
    }


    public function testGetDataWithDefaultAndProvidedEmptyDocument()
    {
        $instance = new JsonApiResponse();
        self::assertEmpty($instance->getData());

        $document = new Document();
        $instance->setDocument($document);
        self::assertEquals('{"meta":[]}', $instance->getData());


        $mock = $this->getMockBuilder(Document::class)
            ->getMock();

        $mock->expects(self::once())->method('export')->willThrowException(new JsonException());

        /** @noinspection PhpParamsInspection */
        $instance->setDocument($mock);
        self::assertEquals('', $instance->getData());
    }
}
