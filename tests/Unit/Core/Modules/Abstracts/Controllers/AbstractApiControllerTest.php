<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractApiController;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;

class AbstractApiControllerTest extends AbstractTestCase
{

    public function testConstructorDefaultVerbGet()
    {
        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        /** @noinspection PhpUndefinedFieldInspection */
        self::assertEquals('GET', $instance->verb);
    }

    public function testConstructorVerbPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        /** @noinspection PhpUndefinedFieldInspection */
        self::assertEquals('POST', $instance->verb);
        unset($_SERVER['REQUEST_METHOD']);
    }

    public function testConstructorVerbDelete()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_HTTP_METHOD'] = 'DELETE';

        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        /** @noinspection PhpUndefinedFieldInspection */
        self::assertEquals('DELETE', $instance->verb);


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

        /** @noinspection PhpUndefinedFieldInspection */
        self::assertEquals('PUT', $instance->verb);


        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['HTTP_X_HTTP_METHOD']);
    }


    public function testInitialiseModelWithDefaults()
    {
        $services = $this->getServices();
        $this->setProperty($services->paths(), 'root', './tests/Mocks/ValidComposerNamespace');

        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$services])
            ->getMockForAbstractClass();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Model not found: index');
        /** @noinspection PhpUndefinedMethodInspection */
        $instance->initialiseModel();
    }


    public function testCompleteRender()
    {
        $instance = $this->getMockBuilder(AbstractApiController::class)
            ->setConstructorArgs([$this->getServices()])
            ->onlyMethods(['saveCache'])
            ->getMockForAbstractClass();

        $instance->expects(self::once())->method('saveCache');

        /** @noinspection PhpUndefinedMethodInspection */
        $instance->completeRender();
    }
}
