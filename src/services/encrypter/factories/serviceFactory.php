<?php
namespace carlonicora\minimalism\services\encrypter\factories;

use carlonicora\minimalism\core\exceptions\configurationException;
use carlonicora\minimalism\core\services\abstracts\abstractServiceFactory;
use carlonicora\minimalism\services\encrypter\configurations\encrypterConfigurations;
use carlonicora\minimalism\services\encrypter\encrypter;
use carlonicora\minimalism\core\services\factories\servicesFactory;

class serviceFactory  extends abstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services) {
        $this->configData = new encrypterConfigurations();

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return encrypter
     */
    public function create(servicesFactory $services) : encrypter {
        return new encrypter($this->configData, $services);
    }
}