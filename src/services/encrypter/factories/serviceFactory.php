<?php
namespace carlonicora\minimalism\services\encrypter\factories;

use carlonicora\minimalism\services\encrypter\configurations\encrypterConfigurations;
use carlonicora\minimalism\services\encrypter\encrypter;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceFactoryInterface;

class serviceFactory implements serviceFactoryInterface {
    /** @var encrypterConfigurations  */
    private encrypterConfigurations $configData;

    /**
     * serviceFactory constructor.
     *
     */
    public function __construct() {
        $this->configData = new encrypterConfigurations();
    }

    /**
     * @param servicesFactory $services
     * @return encrypter
     */
    public function create(servicesFactory $services) : encrypter {
        return new encrypter($this->configData);
    }
}