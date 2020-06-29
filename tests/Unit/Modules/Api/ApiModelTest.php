<?php

namespace CarloNicora\Minimalism\Tests\Unit\Modules\Api;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Modules\Api\ApiModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;
use ReflectionProperty;

class ApiModelTest extends AbstractTestCase
{

    /**
     * @var ApiModel
     */
    private ApiModel $instance;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->instance = new ApiModel($this->getServices());
    }

    public function testDocumentSetters()
    {
        $mock = $this->getMockBuilder(Document::class)
            ->onlyMethods(['setIncludedResourceTypes', 'setRequiredFields'])
            ->getMock();

        // swap default Document for our mock Document
        /** @noinspection PhpUnhandledExceptionInspection */
        $property = new ReflectionProperty($this->instance, 'document');
        $property->setAccessible(true);
        $property->setValue($this->instance, $mock);

        $includedResourceTypes = ['text'];
        $mock->expects($this->once())->method('setIncludedResourceTypes')
            ->with($this->identicalTo($includedResourceTypes));

        $requiredFields = ['a' => [], 'b' => []];
        $mock->expects($this->once())->method('setRequiredFields')
            ->with($this->identicalTo($requiredFields));

        $this->instance->setIncludedResourceTypes($includedResourceTypes);
        $this->instance->setRequiredFields($requiredFields);
    }

    public function testInitialise()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->instance->initialise([], null);
        $this->assertInstanceOf(JsonApiResponse::class, $this->getProperty($this->instance, 'response'));
    }

    public function testDELETE()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->instance->DELETE();
        $this->assertInstanceOf(JsonApiResponse::class, $response);
        $this->assertEquals(405, $response->getStatus());
    }

    public function testGET()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->instance->GET();
        $this->assertInstanceOf(JsonApiResponse::class, $response);
        $this->assertEquals(405, $response->getStatus());
    }

    public function testPOST()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->instance->POST();
        $this->assertInstanceOf(JsonApiResponse::class, $response);
        $this->assertEquals(405, $response->getStatus());
    }

    public function testPUT()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->instance->PUT();
        $this->assertInstanceOf(JsonApiResponse::class, $response);
        $this->assertEquals(405, $response->getStatus());
    }
}
