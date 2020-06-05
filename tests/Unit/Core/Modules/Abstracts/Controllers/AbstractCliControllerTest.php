<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractCliController;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractCliControllerTest extends AbstractTestCase
{

    public function testInitialiseParametersWithDefault()
    {
        $instance = $this->getMockBuilder(AbstractCliController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        $this->markTestSkipped('Needs support for passing in ModelInterface instance before being able to test');
        $instance->initialiseParameters();
    }

    public function testPostInitialise()
    {
        $instance = $this->getMockBuilder(AbstractCliController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        $this->assertSame($instance, $instance->postInitialise());
    }

    public function testCompleteRender()
    {
        $instance = $this->getMockBuilder(AbstractCliController::class)
            ->setConstructorArgs([$this->getServices()])
            ->onlyMethods(['saveCache'])
            ->getMockForAbstractClass();

        $instance->expects($this->once())->method('saveCache');

        $instance->completeRender();
    }
}
