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
        $mock->expects(self::once())->method('setIncludedResourceTypes')
            ->with(self::identicalTo($includedResourceTypes));

        $requiredFields = ['a' => [], 'b' => []];
        $mock->expects(self::once())->method('setRequiredFields')
            ->with(self::identicalTo($requiredFields));

        $this->instance->setIncludedResourceTypes($includedResourceTypes);
        $this->instance->setRequiredFields($requiredFields);
    }

    public function testInitialise()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->instance->initialise([], null);
        self::assertInstanceOf(JsonApiResponse::class, $this->getProperty($this->instance, 'response'));
    }

    public function testDELETE()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->instance->DELETE();
        self::assertInstanceOf(JsonApiResponse::class, $response);
        self::assertEquals(405, $response->getStatus());
    }

    public function testGET()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->instance->GET();
        self::assertInstanceOf(JsonApiResponse::class, $response);
        self::assertEquals(405, $response->getStatus());
    }

    public function testPOST()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->instance->POST();
        self::assertInstanceOf(JsonApiResponse::class, $response);
        self::assertEquals(405, $response->getStatus());
    }

    public function testPUT()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->instance->PUT();
        self::assertInstanceOf(JsonApiResponse::class, $response);
        self::assertEquals(405, $response->getStatus());
    }
}
