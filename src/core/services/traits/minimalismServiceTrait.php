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
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        return self::$services->service(\carlonicora\minimalism\services\database\factories\serviceFactory::class);
    }

    /**
     * @return encrypter
     * @throws serviceNotFoundException
     */
    public static function encrypter():encrypter {
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        return self::$services->service(\carlonicora\minimalism\services\encrypter\factories\serviceFactory::class);
    }

    /**
     * @return paths
     * @throws serviceNotFoundException
     */
    public static function paths():paths {
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        return self::$services->service(\carlonicora\minimalism\services\paths\factories\serviceFactory::class);
    }

    /**
     * @return security
     * @throws serviceNotFoundException
     */
    public static function security() : security {
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        return self::$services->service(\carlonicora\minimalism\services\security\factories\serviceFactory::class);
    }
}