<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Factories;

use CarloNicora\Minimalism\Core\Modules\Factories\ControllerFactory;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use CarloNicora\Minimalism\Tests\Unit\Mocks\SecondaryGenericController;
use Exception;

class ControllerFactoryTest extends AbstractTestCase
{
    /** @var ControllerFactory  */
    private ?ControllerFactory $controllerFactory=null;

    public function setUp(): void
    {
        parent::setUp();

        $this->controllerFactory = new ControllerFactory($this->services);
    }

    /**
     * @throws Exception
     */
    public function testFailToLoadControllerNoControllers() : void
    {
        $this->setProperty($this->controllerFactory, 'controllers', []);

        $this->expectExceptionCode(500);

        $this->controllerFactory->loadController();
    }

    /**
     * @throws Exception
     */
    public function testFailToLoadControllerMultipleControllers() : void
    {
        $this->setProperty($this->controllerFactory, 'controllers', ['1', '2']);

        $this->expectExceptionCode(500);

        $this->controllerFactory->loadController();
    }

    /**
     * @throws Exception
     */
    public function testSuccedLoadingControllerName() : void
    {
        $genericControllerFile = './tests/Unit/Mocks/SecondaryGenericController.php';
        $this->setProperty($this->controllerFactory, 'controllers', [$genericControllerFile]);

        $controller = $this->controllerFactory->loadController();

        $this->assertEquals(new SecondaryGenericController($this->services), $controller);
    }

    /**
     * @throws Exception
     */
    public function testSuccedLoadingControllerNameByLoadingItTwice() : void
    {
        $genericControllerFile = './tests/Unit/Mocks/SecondaryGenericController.php';
        $this->setProperty($this->controllerFactory, 'controllers', [$genericControllerFile]);

        $controller = $this->controllerFactory->loadController();

        $this->assertEquals(new SecondaryGenericController($this->services), $controller);
    }
}