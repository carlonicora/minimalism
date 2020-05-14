<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules;

use CarloNicora\Minimalism\Core\Modules\ErrorController;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use Exception;

class ErrorControllerTest extends AbstractTestCase
{
    public function testInitialisation() : void
    {
        $services = new ServicesFactory();
        $errorController = new ErrorController($services);

        $this->assertEquals($errorController, $errorController->initialise());
    }

    public function testSetException() : void
    {
        $services = new ServicesFactory();
        $errorController = new ErrorController($services);

        $e = new Exception();

        $errorController->setException($e);

        $this->assertEquals($e, $this->getProperty($errorController, 'exception'));
    }

    public function testRender() : void
    {
        $services = new ServicesFactory();
        $errorController = new ErrorController($services);

        $e = new Exception('message', 500);

        $errorController->setException($e);

        $response = new Response();
        $response->httpStatus = $e->getCode();
        $response->data = $e->getMessage();

        $this->assertEquals($response, $errorController->render());
    }
}