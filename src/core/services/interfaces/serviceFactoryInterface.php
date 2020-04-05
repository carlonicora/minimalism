<?php
namespace carlonicora\minimalism\core\services\interfaces;

use carlonicora\minimalism\core\services\exceptions\configurationException;
use carlonicora\minimalism\core\services\factories\servicesFactory;

interface serviceFactoryInterface {
    /**
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services);

    /**
     * @param servicesFactory $services
     * @return mixed
     */
    public function create(servicesFactory $services);
}