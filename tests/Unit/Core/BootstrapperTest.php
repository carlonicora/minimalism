<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core;

use CarloNicora\Minimalism\Core\Bootstrapper;
use CarloNicora\Minimalism\Core\Modules\ErrorController;
use CarloNicora\Minimalism\Tests\Mocks\TestController;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;

class BootstrapperTest extends AbstractTestCase
{

    /**
     * @throws Exception
     */
    public function testDenyAccessToSpecificFileTypes()
    {
        global $_SERVER;

        $deniedExtensions = ['jpg', 'png', 'css', 'js', 'ico'];

        foreach ($deniedExtensions as $deniedExtension) {
            $_SERVER['REQUEST_URI'] = "file.$deniedExtension";
            $instance = new Bootstrapper();

            /** @var ErrorController $controller */
            $controller = $instance->loadController();
            self::assertInstanceOf(ErrorController::class, $controller);
            self::assertEquals('Filetype not supported', $controller->render()->getData());
            self::assertEquals('404', $controller->render()->getStatus());
        }

        unset($_SERVER['REQUEST_URI']);
    }


    public function testLoadControllerWithDefaults()
    {
        $instance = new Bootstrapper();
        $controller = $instance->loadController();

        self::assertInstanceOf(ErrorController::class, $controller);
    }


    /**
     * @throws Exception
     */
    public function testLoadControllerAfterInitialised()
    {
        $instance = new Bootstrapper();
        $instance->initialise(TestController::class);
        $controller = $instance->loadController('test model');

        self::assertInstanceOf(TestController::class, $controller);
    }
}
