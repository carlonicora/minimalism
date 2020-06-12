<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractWebController;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class AbstractWebControllerTest extends AbstractTestCase
{

    public function testPreRender()
    {
        $instance = $this->getMockBuilder(AbstractWebController::class)
            ->setConstructorArgs([$this->getServices()])
            ->onlyMethods(['initialiseView'])
            ->getMockForAbstractClass();
        $instance->expects($this->once())->method('initialiseView')->with();

        $instance->preRender();
    }


    public function testCompleteRender()
    {
        $services = $this->getServices();

        $instance = $this->getMockBuilder(AbstractWebController::class)
            ->setConstructorArgs([$services])
            ->onlyMethods(['setCookie'])
            ->getMockForAbstractClass();

        $instance->expects($this->once())->method('setCookie')->with(
            'minimalismServices',
            '[]',
            (30 * 24 * 60 * 60) // 30 days
        );

        $instance->completeRender();

        $this->assertSame($services, $_SESSION['minimalismServices']);
    }
}
