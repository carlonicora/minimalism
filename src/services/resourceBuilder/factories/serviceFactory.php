<?php
namespace carlonicora\minimalism\services\resourceBuilder\factories;

use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceFactoryInterface;
use carlonicora\minimalism\services\resourceBuilder\resourceBuilder;

class serviceFactory implements serviceFactoryInterface {
    /**
     * serviceFactory constructor.
     *
     */
    public function __construct() {
    }

    /**
     * @param servicesFactory $services
     * @return resourceBuilder
     */
    public function create(servicesFactory $services) : resourceBuilder {
        return new resourceBuilder($services);
    }
}