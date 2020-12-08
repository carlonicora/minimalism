<?php
namespace CarloNicora\Minimalism\Tests\Unit\Services\Paths\Factories;

use CarloNicora\Minimalism\Services\Paths\Configurations\PathsConfigurations;
use CarloNicora\Minimalism\Services\Paths\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\Paths\Paths;
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

        self::assertInstanceOf(ServiceFactory::class, $response);

        return $response;
    }

    /**
     * @param ServiceFactory $service
     * @depends testServiceInitialisation
     * @throws Exception
     */
    public function testServiceCreation(ServiceFactory $service) : void
    {
        $config = new PathsConfigurations();
        $services = $this->getServices();
        $paths = new Paths($config, $services);

        self::assertEquals($paths, $service->create($services));
    }
}