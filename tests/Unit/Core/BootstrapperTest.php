<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core;

use CarloNicora\Minimalism\Core\Bootstrapper;
use CarloNicora\Minimalism\Core\Modules\ErrorController;
use CarloNicora\Minimalism\Tests\Mocks\TestController;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class BootstrapperTest extends AbstractTestCase
{

    public function testDenyAccessToSpecificFileTypes()
    {
        global $_SERVER;

        $deniedExtensions = ['jpg', 'png', 'css', 'js', 'ico'];

        foreach ($deniedExtensions as $deniedExtension) {
            $_SERVER['REQUEST_URI'] = "file.$deniedExtension";
            $instance = new Bootstrapper();

            /** @var ErrorController $controller */
            $controller = $instance->loadController();
            $this->assertInstanceOf(ErrorController::class, $controller);
            $this->assertEquals('Filetype not supported', $controller->render()->getData());
            $this->assertEquals('404', $controller->render()->getStatus());
        }

        unset($_SERVER['REQUEST_URI']);
    }


    public function testLoadControllerWithDefaults()
    {
        $instance = new Bootstrapper();
        $controller = $instance->loadController();

        $this->assertInstanceOf(ErrorController::class, $controller);
    }


    public function testLoadControllerAfterInitialised()
    {
        $instance = new Bootstrapper();
        $instance->initialise(TestController::class);
        $controller = $instance->loadController('test model');

        $this->assertInstanceOf(TestController::class, $controller);
    }
}
