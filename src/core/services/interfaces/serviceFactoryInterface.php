<?php
namespace carlonicora\minimalism\core\services\interfaces;

use carlonicora\minimalism\core\exceptions\configurationException;
use carlonicora\minimalism\core\services\factories\servicesFactory;

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