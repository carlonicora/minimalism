<?php

namespace CarloNicora\Minimalism\Tests\Unit\Modules\Web;

use CarloNicora\Minimalism\Core\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Modules\Web\Extensions\TwigExtensions;
use CarloNicora\Minimalism\Modules\Web\WebController;
use CarloNicora\Minimalism\Modules\Web\WebModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;

class WebControllerTest extends AbstractTestCase
{

    /**
     * @todo create a mock twig view in order to test success/failure execution
     * @throws Exception
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
        $modelMock->expects(self::exactly(3))->method('getViewName')->willReturn('mock');
        $modelMock->expects(self::once())->method('generateData')->willReturn($responseMock);
        $modelMock->expects(self::once())->method('getTwigExtensions')->willReturn([new TwigExtensions()]);


        $instance->initialiseModel($modelMock);
        $response = $instance->render();

        self::assertEquals('', $response->getStatus());
    }
}
