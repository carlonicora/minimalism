<?php

namespace CarloNicora\Minimalism\Tests\Unit\Modules\Cli;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Modules\Cli\CliController;
use CarloNicora\Minimalism\Modules\Cli\CliModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class CliControllerTest extends AbstractTestCase
{

    public function testRender()
    {
        $instance = new CliController($this->getServices());

        $modelMock = $this->getMockBuilder(CliModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();

        $responseMock = $this->getMockBuilder(ResponseInterface::class)->getMock();


        $modelMock->method('redirect')->willReturn('');
        $modelMock->expects($this->at(0))->method('run')->willReturn($responseMock);

        $instance->initialiseModel($modelMock);
        $instance->render();

        $modelMock->expects($this->at(1))->method('run')->willThrowException(new \Exception());
        $this->assertInstanceOf(Response::class, $instance->render());

    }
}
