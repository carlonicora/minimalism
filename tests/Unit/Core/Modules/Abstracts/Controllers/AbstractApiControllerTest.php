<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractApiController;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class AbstractApiControllerTest extends AbstractTestCase
{

    public function testConstructorDefaultVerbGet()
    {
        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        $this->assertEquals('GET', $instance->verb);
    }

    public function testConstructorVerbPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        $this->assertEquals('POST', $instance->verb);
        unset($_SERVER['REQUEST_METHOD']);
    }

    public function testConstructorVerbDelete()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_HTTP_METHOD'] = 'DELETE';

        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        $this->assertEquals('DELETE', $instance->verb);


        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['HTTP_X_HTTP_METHOD']);
    }

    public function testConstructorVerbPut()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_HTTP_METHOD'] = 'PUT';

        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        $this->assertEquals('PUT', $instance->verb);


        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['HTTP_X_HTTP_METHOD']);
    }


    public function testInitialiseModelWithDefaults()
    {
        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        $this->markTestSkipped('We cannot use the mock TestModel as the model namespace is prefixed within the method');
        $instance->initialiseModel('Tests\\Mocks\\TestModel');
    }


    public function testCompleteRender()
    {
        /**
        $sfMock = $this->getMockBuilder(ServicesFactory::class)
            ->onlyMethods(['cleanNonPersistentVariables', 'destroyStatics'])
            ->getMock();

        $sfMock->expects($this->once())->method('cleanNonPersistentVariables');
        $sfMock->expects($this->once())->method('destroyStatics');
         * */

        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$this->getServices()])
            ->onlyMethods(['saveCache'])
            ->getMockForAbstractClass();

        $instance->expects($this->once())->method('saveCache');

        $instance->completeRender();
    }
}
