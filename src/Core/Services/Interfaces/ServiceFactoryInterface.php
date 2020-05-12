<?php
namespace CarloNicora\Minimalism\Core\Services\Interfaces;

use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;

interface ServiceFactoryInterface {
    /**
     * @param ServicesFactory $services
     * @throws ConfigurationException
     */
    public function __construct(ServicesFactory $services);

    /**
     * @param ServicesFactory $services
     * @return mixed
     */
    public function create(ServicesFactory $services);
}