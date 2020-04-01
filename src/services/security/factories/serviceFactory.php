<?php
namespace carlonicora\minimalism\services\security\factories;

use carlonicora\minimalism\exceptions\serviceNotFoundException;
use carlonicora\minimalism\services\factories\servicesFactory;
use carlonicora\minimalism\services\interfaces\serviceFactoryInterface;
use carlonicora\minimalism\services\security\configurations\securityConfigurations;
use carlonicora\minimalism\services\security\security;

class serviceFactory implements serviceFactoryInterface {
    /** @var securityConfigurations  */
    private securityConfigurations $configData;

    /**
     * serviceFactory constructor.
     */
    public function __construct() {
        $this->configData = new securityConfigurations();
    }

    /**
     * @param servicesFactory $services
     * @return security|mixed
     * @throws serviceNotFoundException
     */
    public function create(servicesFactory $services) {
        $this->configData->setupSecurityInterfaces($services);
        return new security($this->configData);
    }
}