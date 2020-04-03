<?php
namespace carlonicora\minimalism\core\services\interfaces;

use carlonicora\minimalism\core\services\factories\servicesFactory;

interface serviceFactoryInterface {
    /**
     * @param servicesFactory $services
     */
    public function __construct(servicesFactory $services);

    /**
     * @param servicesFactory $services
     * @return mixed
     */
    public function create(servicesFactory $services);
}