<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules;

use CarloNicora\Minimalism\Core\Modules\ErrorController;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use Exception;

class ErrorControllerTest extends AbstractTestCase
{
    public function testInitialisation() : void
    {
        $errorController = new ErrorController($this->services);

        $this->assertEquals($errorController, $errorController->initialiseParameters([])->initialiseModel(''));
    }

    public function testSetException() : void
    {
        $errorController = new ErrorController($this->services);

        $e = new Exception();

        $errorController->setException($e);

        $this->assertEquals($e, $this->getProperty($errorController, 'exception'));
    }

    public function testRender() : void
    {
        $errorController = new ErrorController($this->services);

        $e = new Exception('message', 500);

        $errorController->setException($e);

        $response = new Response();
        $response->setStatus($e->getCode());
        $response->setData($e->getMessage());

        $this->assertEquals($response, $errorController->render());
    }

    public function testCompleteRender() : void
    {
        $errorController = new ErrorController($this->services);

        $e = new Exception('message', 500);

        $errorController->setException($e);

        $response = new Response();
        $response->setStatus($e->getCode());
        $response->setData($e->getMessage());

        $errorController->render();

        $errorController->completeRender();

        $this->assertEquals(1,1);
    }
}