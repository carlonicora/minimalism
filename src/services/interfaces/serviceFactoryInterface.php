<?php
namespace carlonicora\minimalism\services\interfaces;

use carlonicora\minimalism\exceptions\configurationException;
use carlonicora\minimalism\services\factories\servicesFactory;

interface serviceFactoryInterface {
    /**
     * @throws configurationException
     */
    public function __construct();

    /**
     * @param servicesFactory $services
     * @return mixed
     */
    public function create(servicesFactory $services);
}