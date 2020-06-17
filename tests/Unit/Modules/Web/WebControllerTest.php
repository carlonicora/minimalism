<?php

namespace CarloNicora\Minimalism\Tests\Unit\Modules\Web;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Modules\Web\Extensions\TwigExtensions;
use CarloNicora\Minimalism\Modules\Web\WebController;
use CarloNicora\Minimalism\Modules\Web\WebModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class WebControllerTest extends AbstractTestCase
{

    /**
     * @todo create a mock twig view in order to test success/failure execution
     * @throws \Exception
     */
    public function testRender()
    {
        $instance = new WebController($this->getServices());

        $modelMock = $this->getMockBuilder(WebModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();

        $responseMock = $this->getMockBuilder(JsonApiResponse::class)
            ->getMock();

        $modelMock->method('redirect')->willReturn('');
        $modelMock->expects($this->exactly(3))->method('getViewName')->willReturn('mock');
        $modelMock->expects($this->once())->method('generateData')->willReturn($responseMock);
        $modelMock->expects($this->once())->method('getTwigExtensions')->willReturn([new TwigExtensions()]);


        $instance->initialiseModel($modelMock);
        $response = $instance->render();

        $this->assertEquals('', $response->getStatus());
    }


    public function testPostInitialise()
    {
        $instance = new WebController($this->getServices());

        $this->assertSame($instance, $instance->postInitialise());
    }
}
