<?php
namespace carlonicora\minimalism\services\logger\factories;

use carlonicora\minimalism\core\services\abstracts\abstractServiceFactory;
use carlonicora\minimalism\core\services\exceptions\configurationException;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\logger\configurations\loggerConfigurations;
use carlonicora\minimalism\services\logger\logger;
use carlonicora\minimalism\services\paths\paths;

class serviceFactory extends abstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException|serviceNotFoundException
     */
    public function __construct(servicesFactory $services) {
        /** @var paths $path */
        $path = $services->service(paths::class);

        $this->configData = new loggerConfigurations($path->getRoot());

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return logger
     */
    public function create(servicesFactory $services) : logger {
        return new logger($this->configData, $services);
    }
}