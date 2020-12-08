<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractCliController;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

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

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertSame($instance, $instance->initialiseParameters());
    }

    public function testCompleteRender()
    {
        $instance = $this->getMockBuilder(AbstractCliController::class)
            ->setConstructorArgs([$this->getServices()])
            ->onlyMethods(['saveCache'])
            ->getMockForAbstractClass();

        $instance->expects(self::once())->method('saveCache');

        /** @noinspection PhpUndefinedMethodInspection */
        $instance->completeRender();
    }
}
