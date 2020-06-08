<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractModel;
use CarloNicora\Minimalism\Services\ParameterValidator\Commands\DecrypterCommand;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractModelTest extends AbstractTestCase
{
    /** @var AbstractModel */
    private MockObject $instance;

    public function setUp(): void
    {
        $this->instance = $this->getMockBuilder(AbstractModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();
    }

    public function testGetResponseFromError()
    {
        $exception = new \Exception('test message', 408);

        $this->assertEquals('408', $this->instance->getResponseFromError($exception)->getStatus());
        $this->assertEquals('test message', $this->instance->getResponseFromError($exception)->getData());
    }


    public function testRedirectWithDefault()
    {
        $this->assertEmpty($this->instance->redirect());
    }


    public function testGetParametersWithDefault()
    {
        $this->assertEmpty($this->instance->getParameters());
    }


    public function testSetParameterCreatesPublicProperty()
    {
        $this->assertFalse(\property_exists($this->instance, 'test_data'));

        $this->instance->setParameter('test_data', 2);

        $this->assertTrue(\property_exists($this->instance, 'test_data'));
        $this->assertEquals(2, $this->instance->test_data);
    }


    public function testDecrypterWithDefault()
    {
        $this->assertInstanceOf(DecrypterCommand::class, $this->instance->decrypter());
    }
}