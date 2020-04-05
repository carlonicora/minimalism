<?php
namespace carlonicora\minimalism\core\services\traits;

use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\services\database\database;
use carlonicora\minimalism\services\encrypter\encrypter;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\paths\paths;
use carlonicora\minimalism\services\security\security;

trait minimalismServiceTrait {
    /** @var servicesFactory */
    protected static servicesFactory $services;

    /**
     * @return database
     * @throws serviceNotFoundException
     */
    public static function database() : database {
        return self::$services->service(database::class);
    }

    /**
     * @return encrypter
     * @throws serviceNotFoundException
     */
    public static function encrypter():encrypter {
        return self::$services->service(encrypter::class);
    }

    /**
     * @return paths
     * @throws serviceNotFoundException
     */
    public static function paths():paths {
        return self::$services->service(paths::class);
    }

    /**
     * @return security
     * @throws serviceNotFoundException
     */
    public static function security() : security {
        return self::$services->service(security::class);
    }
}