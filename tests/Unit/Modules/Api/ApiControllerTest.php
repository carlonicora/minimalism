<?php

namespace CarloNicora\Minimalism\Tests\Unit\Modules\Api;

use CarloNicora\Minimalism\Core\Modules\ErrorController;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Interfaces\SecurityInterface;
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


        $mock->expects($this->any())->method('redirect')->willReturn('');
        $instance->initialiseModel($mock);


        $this->setProperty($instance, 'passedParameters', [
            'include' => 'x,y,z'
        ]);
        $mock->expects($this->once())->method('setIncludedResourceTypes')->with(['x', 'y', 'z']);
        $instance->initialiseModel($mock);


        $this->setProperty($instance, 'passedParameters', [
            'fields' => [ 'type1' => 'value1,value2', 'type2' => 'value3,value4' ]
        ]);
        $mock->expects($this->once())->method('setRequiredFields')->with([
            'type1' => ['value1', 'value2'],
            'type2' => ['value3', 'value4']
        ]);
        $instance->initialiseModel($mock);
    }


    /**
     * @throws Exception
     */
    public function testPostInitialiseWithDefaults()
    {
        $instance = new ApiController($this->getServices());

        $errorController = $instance->postInitialise();
        $this->assertInstanceOf(ErrorController::class, $errorController);
        $this->assertEquals('Undefined index: REQUEST_URI', $errorController->render()->getData());
    }


    /**
     * @throws Exception
     */
    public function testPostInitialiseWithData()
    {
        global $_SERVER;
        $instance = new ApiController($this->getServices());

        $_SERVER['REQUEST_URI'] = '/test';
        $this->assertSame($instance, $instance->postInitialise());


        $mock = $this->getMockBuilder(SecurityInterface::class)->getMock();
        $mock->expects($this->at(0))->method('isSignatureValid')->willReturn(true);
        $mock->expects($this->at(1))->method('isSignatureValid')->willReturn(false);
        $this->setProperty($instance, 'security', $mock);

        $this->assertSame($instance, $instance->postInitialise());

        $errorController = $instance->postInitialise();
        $this->assertInstanceOf(ErrorController::class, $errorController);
        $this->assertEquals('Unauthorised', $errorController->render()->getData());
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

        $responseMock = $this->getMockBuilder(ResponseInterface::class)->getMock();

        $modelMock->expects($this->once())->method('redirect')->willReturn('');
        $modelMock->expects($this->at(0))->method('GET')->willReturn($responseMock);

        $instance->initialiseModel($modelMock);
        $instance->render();

        $modelMock->expects($this->at(1))->method('GET')->willThrowException(new Exception());
        $instance->render();
    }
}
