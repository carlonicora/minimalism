<?php
namespace carlonicora\minimalism\core\services\traits;

use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\paths\paths;

trait minimalismServiceTrait {
    /** @var servicesFactory */
    protected static servicesFactory $services;

    /**
     * @return paths
     * @throws serviceNotFoundException
     */
    public static function paths():paths {
        return self::$services->service(paths::class);
    }
}