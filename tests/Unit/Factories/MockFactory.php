<?php


namespace CarloNicora\Minimalism\Tests\Unit\Factories;


use CarloNicora\Minimalism\Core\Bootstrapper;
use CarloNicora\Minimalism\Core\Modules\Factories\ControllerFactory;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Tests\Unit\Traits\MethodReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use CarloNicora\Minimalism\Tests\Unit\Helpers\GenericController;

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
                new ConfigurationException('minimalism', '', ConfigurationException::ERROR_CONFIGURATION_FILE_ERROR)
            );

        return $response;
    }

    /**
     * @return MockObject|ControllerFactory
     */
    public function createControllerFactoryWithoutModules() : MockObject
    {
        $response = $this->getMockBuilder(ControllerFactory::class)
            ->onlyMethods(['loadControllerName'])
            ->getMock();

        $response->expects($this->once())
            ->method('loadControllerName')
            ->willThrowException(
                new ConfigurationException('minimalism', '', ConfigurationException::ERROR_NO_MODULE_AVAILABLE)
            );

        return $response;
    }

    /**
     * @return MockObject|ControllerFactory
     */
    public function createControllerFactory() : MockObject
    {
        $response = $this->getMockBuilder(ControllerFactory::class)
            ->onlyMethods(['loadControllerName'])
            ->getMock();

        require_once realpath('.') . DIRECTORY_SEPARATOR
            . 'tests' . DIRECTORY_SEPARATOR
            . 'Unit' . DIRECTORY_SEPARATOR
            . 'Helpers' . DIRECTORY_SEPARATOR
            . 'GenericController.php';

        $response->expects($this->once())
            ->method('loadControllerName')
            ->willReturn(
                GenericController::class
            );

        return $response;
    }
}