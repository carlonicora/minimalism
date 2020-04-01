<?php
namespace carlonicora\minimalism\services\paths\factories;

use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceFactoryInterface;
use carlonicora\minimalism\services\paths\paths;
use Exception;

class serviceFactory implements serviceFactoryInterface {
    /**
     * serviceFactory constructor.
     */
    public function __construct() {
    }

    /**
     * @param servicesFactory $services
     * @return paths
     * @throws Exception
     */
    public function create(servicesFactory $services) : paths {
        return new paths();
    }
}