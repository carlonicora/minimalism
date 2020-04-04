<?php
namespace carlonicora\minimalism\services\security\factories;

use carlonicora\minimalism\core\services\exceptions\configurationException;
use carlonicora\minimalism\core\services\abstracts\abstractServiceFactory;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\security\configurations\securityConfigurations;
use carlonicora\minimalism\services\security\security;

class serviceFactory extends abstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services) {
        $this->configData = new securityConfigurations();

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return security|mixed
     */
    public function create(servicesFactory $services) {
        $this->configData->setupSecurityInterfaces($services);
        return new security($this->configData, $services);
    }
}