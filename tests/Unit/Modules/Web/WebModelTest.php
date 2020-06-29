<?php

namespace CarloNicora\Minimalism\Tests\Unit\Modules\Web;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\Error;
use CarloNicora\Minimalism\Core\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Modules\Web\WebModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;
use RuntimeException;
use Twig\Extension\ExtensionInterface;

class WebModelTest extends AbstractTestCase
{

    private MockObject $instance;

    public function setUp(): void
    {
        $this->instance = $this->getMockBuilder(WebModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();
    }

    /**
     * @noinspection PhpUndefinedMethodInspection
     */
    public function testTwigExtensions()
    {
        $extension_one = $this->getMockBuilder(ExtensionInterface::class)->getMock();
        $extension_two = $this->getMockBuilder(ExtensionInterface::class)->getMock();

        $this->instance->addTwigExtension($extension_one);
        $this->instance->addTwigExtension($extension_two);

        $this->assertEquals([$extension_one, $extension_two], $this->instance->getTwigExtensions());
    }

    public function testGenerateData()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertInstanceOf(JsonApiResponse::class, $this->instance->generateData());
    }

    /**
     * @noinspection PhpUndefinedMethodInspection
     */
    public function testPreRender()
    {
        $this->instance->preRender(); // should do nothing

        /** @noinspection PhpUnhandledExceptionInspection */
        $property = new ReflectionProperty($this->instance, 'error');
        $property->setAccessible(true);
        /**
         * @todo
         * As an exception is rethrown based on the set Error object, at minimum a status code and detail need
         * to be provided, otherwise the code will fail with a runtime error
         */
        $property->setValue($this->instance, new Error(null, 101, 'test detail'));

        $this->expectException(RuntimeException::class);
        $this->instance->preRender();
    }

    public function testValidateJsonapiParameter()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertInstanceOf(Document::class, $this->instance->validateJsonapiParameter(null));
    }
}
