<?php

namespace CarloNicora\Minimalism\Tests\Unit\Modules\Api;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Modules\Api\ApiController;
use CarloNicora\Minimalism\Modules\Api\ApiModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;

class ApiControllerTest extends AbstractTestCase
{

    /**
     * @throws Exception
     */
    public function testInitialiseModelWithDefaults()
    {
        $services = $this->getServices();
        $this->setProperty($services->paths(), 'root', './tests/Mocks/ValidComposerNamespace');

        $instance = new ApiController($services);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Model not found: index');

        global $_SERVER;
        $_SERVER['REQUEST_URI'] = '/test';
        $instance->initialiseModel();
    }


    /**
     * @throws Exception
     */
    public function testInitialiseModelWithModel()
    {
        $instance = new ApiController($this->getServices());

        $mock = $this->getMockBuilder(ApiModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();

        global $_SERVER;
        $_SERVER['REQUEST_URI'] = '/test';

        $mock->method('redirect')->willReturn('');
        $instance->initialiseModel($mock);


        $this->setProperty($instance, 'passedParameters', [
            'include' => 'x,y,z'
        ]);
        $mock->expects(self::once())->method('setIncludedResourceTypes')->with(['x', 'y', 'z']);
        $instance->initialiseModel($mock);


        $this->setProperty($instance, 'passedParameters', [
            'fields' => [ 'type1' => 'value1,value2', 'type2' => 'value3,value4' ]
        ]);
        $mock->expects(self::once())->method('setRequiredFields')->with([
            'type1' => ['value1', 'value2'],
            'type2' => ['value3', 'value4']
        ]);
        $instance->initialiseModel($mock);
    }

    /**
     * @throws Exception
     */
    public function testRender()
    {
        $instance = new ApiController($this->getServices());

        $modelMock = $this->getMockBuilder(ApiModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();

        global $_SERVER;
        $_SERVER['REQUEST_URI'] = '/test';

        $responseMock = $this->getMockBuilder(ResponseInterface::class)->getMock();

        $modelMock->expects(self::once())->method('redirect')->willReturn('');
        $modelMock->expects(self::at(0))->method('GET')->willReturn($responseMock);

        $instance->initialiseModel($modelMock);
        $instance->render();

        $modelMock->expects(self::at(1))->method('GET')->willThrowException(new Exception());
        $instance->render();
    }
}
