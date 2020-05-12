<?php
namespace CarloNicora\Minimalism\Core\Services\Traits;

use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Paths\Paths;

trait MinimalismServiceTrait {
    /** @var ServicesFactory */
    protected static ServicesFactory $services;

    /**
     * @return Paths
     * @throws ServiceNotFoundException
     */
    public static function paths():Paths {
        return self::$services->service(Paths::class);
    }
}