<?php
namespace CarloNicora\Minimalism\Tests\Unit\Services\ParametersValidator\Factories;

use CarloNicora\Minimalism\Services\ParameterValidator\Configurations\ParameterValidatorConfigurations;
use CarloNicora\Minimalism\Services\ParameterValidator\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\ParameterValidator;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;

class ServiceFactoryTest extends AbstractTestCase
{
    /**
     * @return ServiceFactory
     */
    public function testServiceInitialisation() : ServiceFactory
    {
        $response = new ServiceFactory($this->getServices());

        $this->assertInstanceOf(ServiceFactory::class, $response);

        return $response;
    }

    /**
     * @param ServiceFactory $service
     * @depends testServiceInitialisation
     * @throws Exception
     */
    public function testServiceCreation(ServiceFactory $service) : void
    {
        $config = new ParameterValidatorConfigurations();
        $services = $this->getServices();
        $paths = new ParameterValidator($config, $services);

        $this->assertEquals($paths, $service->create($services));
    }
}