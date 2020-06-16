<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractCliController;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractCliControllerTest extends AbstractTestCase
{

    /**
     * @note nothing to assert at the moment. $_SERVER['argv'] contains the PHPUnit invocation command
     * line, and it falls through the initialiseParameters method.
     * @see AbstractCliController::initialiseParameters()
     */
    public function testInitialiseParametersWithDefault()
    {
        $instance = $this->getMockBuilder(AbstractCliController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        $this->assertSame($instance, $instance->initialiseParameters());
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
