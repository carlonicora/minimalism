<?php
namespace carlonicora\minimalism\services\apiCaller\factories;

use carlonicora\minimalism\services\apiCaller\apiCaller;
use carlonicora\minimalism\services\apiCaller\configurations\apiCallerConfigurations;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceFactoryInterface;

class serviceFactory implements serviceFactoryInterface {
    /** @var apiCallerConfigurations  */
    private apiCallerConfigurations $configData;

    /**
     * serviceFactory constructor.
     */
    public function __construct() {
        $this->configData = new apiCallerConfigurations();
    }

    /**
     * @param servicesFactory $services
     * @return apiCaller
     */
    public function create(servicesFactory $services): apiCaller {
        return new apiCaller($this->configData, $services);
    }
}