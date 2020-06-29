<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Factories;

use CarloNicora\Minimalism\Core\Modules\ErrorController;
use CarloNicora\Minimalism\Core\Modules\Factories\ControllerFactory;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;

class ControllerFactoryTest extends AbstractTestCase
{

    /**
     * @var ControllerFactory
     */
    private ControllerFactory $instance;

    public function setUp(): void
    {
        $this->instance = new ControllerFactory($this->getServices());
    }

    /**
     * @throws Exception
     */
    public function testLoadControllerWithNoClassName()
    {
        $this->expectException(ConfigurationException::class);
        $this->instance->loadController(null);
    }


    /**
     * @throws Exception
     */
    public function testLoadControllerWithInvalidClassName()
    {
        $this->expectException(ConfigurationException::class);
        $this->instance->loadController('x');
    }


    /**
     * @throws Exception
     */
    public function testLoadControllerWithErrorController()
    {
        $controller = $this->instance->loadController(ErrorController::class);
        $this->assertInstanceOf(ErrorController::class, $controller);
    }
}
