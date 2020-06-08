<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\JsonApi;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

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
    }
}