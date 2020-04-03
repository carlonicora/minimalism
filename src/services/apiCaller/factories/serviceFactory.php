<?php
namespace carlonicora\minimalism\services\apiCaller\factories;

use carlonicora\minimalism\core\exceptions\configurationException;
use carlonicora\minimalism\core\services\abstracts\abstractServiceFactory;
use carlonicora\minimalism\services\apiCaller\apiCaller;
use carlonicora\minimalism\services\apiCaller\configurations\apiCallerConfigurations;
use carlonicora\minimalism\core\services\factories\servicesFactory;

class serviceFactory extends abstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services) {
        $this->configData = new apiCallerConfigurations();

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return apiCaller
     */
    public function create(servicesFactory $services): apiCaller {
        return new apiCaller($this->configData, $services);
    }
}