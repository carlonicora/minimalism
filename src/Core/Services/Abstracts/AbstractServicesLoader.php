<?php
namespace CarloNicora\Minimalism\Core\Services\Abstracts;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Core\Services\Traits\MinimalismServiceTrait;

abstract class AbstractServicesLoader {
    use MinimalismServiceTrait;

    /**
     * @param ServicesFactory $services
     */
    public static function initialise(ServicesFactory $services) : void {
        self::$services = $services;
    }
}