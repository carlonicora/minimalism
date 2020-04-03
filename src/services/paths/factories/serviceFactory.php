<?php
namespace carlonicora\minimalism\services\paths\factories;

use carlonicora\minimalism\core\exceptions\configurationException;
use carlonicora\minimalism\core\services\abstracts\abstractServiceFactory;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\paths\configurations\pathsConfigurations;
use carlonicora\minimalism\services\paths\paths;
use Exception;

class serviceFactory extends abstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services) {
        $this->configData = new pathsConfigurations();

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return paths
     * @throws Exception
     */
    public function create(servicesFactory $services) : paths {
        return new paths($this->configData, $services);
    }
}