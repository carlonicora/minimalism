<?php
namespace CarloNicora\Minimalism\Services\Paths\Factories;

use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceFactory;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Paths\configurations\PathsConfigurations;
use CarloNicora\Minimalism\Services\Paths\Paths;
use Exception;

class ServiceFactory extends AbstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param ServicesFactory $services
     * @throws ConfigurationException
     */
    public function __construct(ServicesFactory $services) {
        $this->configData = new PathsConfigurations();

        parent::__construct($services);
    }

    /**
     * @param ServicesFactory $services
     * @return Paths
     * @throws Exception
     */
    public function create(ServicesFactory $services) : Paths {
        return new Paths($this->configData, $services);
    }
}