<?php
namespace carlonicora\minimalism\core\services\abstracts;

use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\traits\minimalismServiceTrait;

abstract class abstractServicesLoader {
    use minimalismServiceTrait;

    /**
     * @param servicesFactory $services
     */
    final public static function initialise(servicesFactory $services) : void {
        self::$services = $services;
    }
}