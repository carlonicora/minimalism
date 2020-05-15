<?php
namespace CarloNicora\Minimalism\Core\Services\Abstracts;

use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceConfigurationsInterface;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceFactoryInterface;

abstract class AbstractServiceFactory implements ServiceFactoryInterface
{
    /** @var ServiceConfigurationsInterface  */
    protected ServiceConfigurationsInterface $configData;

    /** @var ServicesFactory  */
    protected ServicesFactory $services;

    /**
     * abstractServiceFactory constructor.
     * @param ServicesFactory $services
     * @throws ConfigurationException
     */
    public function __construct(ServicesFactory $services)
    {
        $this->services = $services;
        $this->setupDependencies();
    }

    /**
     * @param ServicesFactory $services
     * @return mixed
     */
    abstract public function create(ServicesFactory $services);

    /**
     *
     * @throws ConfigurationException
     */
    private function setupDependencies(): void
    {
        foreach ($this->configData->getDependencies() ?? [] as $serviceName) {
            $this->services->loadDependency($serviceName);
        }
    }
}