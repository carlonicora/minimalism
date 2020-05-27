<?php


namespace CarloNicora\Minimalism\Tests\Unit\Factories;


use CarloNicora\Minimalism\Core\Bootstrapper;
use CarloNicora\Minimalism\Core\Modules\Factories\ControllerFactory;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Factories\ServiceFactory;
use CarloNicora\Minimalism\Tests\Unit\Traits\MethodReflectionTrait;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use CarloNicora\Minimalism\Tests\Unit\Mocks\GenericController;

class MockFactory extends TestCase
{
    use MethodReflectionTrait;

    /**
     * @return MockObject|Bootstrapper
     */
    public function createBootstrapper() : MockObject
    {
        return $this->getMockBuilder(Bootstrapper::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject|ServicesFactory
     */
    public function createNonInitialisingServicesFactory() : MockObject
    {
        $response = $this->getMockBuilder(ServicesFactory::class)
            ->onlyMethods(['initialise'])
            ->getMock();

        $response->expects($this->once())
            ->method('initialise')
            ->willThrowException(
                new ConfigurationException('', 500)
            );

        return $response;
    }

    /**
     * @return MockObject|ControllerFactory
     */
    public function createControllerFactoryWithoutModules() : MockObject
    {
        $services = new ServicesFactory();

        $response = $this->getMockBuilder(ControllerFactory::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$services])
            ->onlyMethods(['loadController'])
            ->getMock();

        $response->expects($this->once())
            ->method('loadController')
            ->willThrowException(
                new ConfigurationException('', 500)
            );

        return $response;
    }

    /**
     * @return MockObject|ControllerFactory
     * @throws Exception
     */
    public function createControllerFactory() : MockObject
    {
        $services = new ServicesFactory();
        $services->loadService(ServiceFactory::class);

        $response = $this->getMockBuilder(ControllerFactory::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$services])
            ->onlyMethods(['loadController'])
            ->getMock();

        /** @noinspection PhpIncludeInspection */
        require_once realpath('.') . DIRECTORY_SEPARATOR
            . 'tests' . DIRECTORY_SEPARATOR
            . 'Unit' . DIRECTORY_SEPARATOR
            . 'Mocks' . DIRECTORY_SEPARATOR
            . 'GenericController.php';

        $response->expects($this->once())
            ->method('loadController')
            ->willReturn(
                new GenericController($services)
            );

        return $response;
    }
}