<?php
namespace carlonicora\minimalism\core\services\abstracts;

use carlonicora\minimalism\core\services\exceptions\configurationException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;
use carlonicora\minimalism\core\services\interfaces\serviceFactoryInterface;

abstract class abstractServiceFactory implements serviceFactoryInterface {
    /** @var serviceConfigurationsInterface  */
    protected serviceConfigurationsInterface $configData;

    /** @var servicesFactory  */
    protected servicesFactory $services;

    /**
     * abstractServiceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services) {
        $this->services = $services;
        $this->setupDependencies();
    }

    /**
     * @param servicesFactory $services
     * @return mixed
     */
    abstract public function create(servicesFactory $services);

    /**
     *
     * @throws configurationException
     */
    private function setupDependencies(): void {
        foreach ($this->configData->getDependencies() ?? [] as $serviceName) {
            $this->services->loadDependency($serviceName);
        }
    }
}