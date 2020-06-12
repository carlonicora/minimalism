<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractController;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class AbstractControllerTest extends AbstractTestCase
{
    public function testGetPhpInputParametersCalledByInitialiseParameters(): void
    {
        $mock = $this->getMockBuilder(AbstractController::class)
            ->onlyMethods([
                'getPhpInputParameters'
            ])
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();
        $mock->expects($this->once())->method('getPhpInputParameters')->with();

        $mock->initialiseParameters();
    }
}

