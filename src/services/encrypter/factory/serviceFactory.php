<?php
namespace carlonicora\minimalism\services\encrypter\factory;

use carlonicora\minimalism\services\encrypter\configurations\encrypterConfigurations;
use carlonicora\minimalism\services\encrypter\encrypter;
use carlonicora\minimalism\services\interfaces\serviceFactoryInterface;

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
     * @return encrypter
     */
    public function create() : encrypter {
        return new encrypter($this->configData);
    }


}