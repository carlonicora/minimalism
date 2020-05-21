<?php
namespace CarloNicora\Minimalism\Services\Logger\Factories;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceFactory;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Configurations\LoggerConfigurations;
use CarloNicora\Minimalism\Services\Logger\Logger;

class ServiceFactory extends AbstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param ServicesFactory $services
     * @throws ConfigurationException
     */
    public function __construct(ServicesFactory $services) {
        $this->configData = new LoggerConfigurations();

        parent::__construct($services);
    }

    /**
     * @param ServicesFactory $services
     * @return Logger
     * @throws ServiceNotFoundException
     */
    public function create(ServicesFactory $services) : Logger {
        return new Logger($this->configData, $services);
    }
}