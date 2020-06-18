<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractWebController;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
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
        $servicesMock = $this->getMockBuilder(ServicesFactory::class)->getMock();
        $instance = $this->getMockBuilder(AbstractWebController::class)
            ->setConstructorArgs([$servicesMock])
            ->onlyMethods(['setCookie'])
            ->getMockForAbstractClass();

        $servicesMock->expects($this->once())->method('serialiseCookies')->willReturn('[]');
        $instance->expects($this->once())->method('setCookie')->with(
            'minimalismServices',
            '[]',
            (30 * 24 * 60 * 60) // 30 days
        );

        $instance->completeRender();
        $this->assertSame($servicesMock, $_SESSION['minimalismServices']);
        unset($_SESSION['minimalismServices']);
    }


    public function testCompleteRenderWithException()
    {
        $servicesMock = $this->getMockBuilder(ServicesFactory::class)->getMock();

        $instance = $this->getMockBuilder(AbstractWebController::class)
            ->setConstructorArgs([$servicesMock])
            ->onlyMethods(['setCookie'])
            ->getMockForAbstractClass();

        $servicesMock->expects($this->once())->method('serialiseCookies')->willThrowException(new \JsonException());
        $servicesMock->expects($this->exactly(2))->method('logger');
        $instance->completeRender();
        unset($_SESSION['minimalismServices']);
    }
}
