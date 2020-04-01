<?php
namespace carlonicora\minimalism\services;

use carlonicora\minimalism\exceptions\serviceNotFoundException;
use carlonicora\minimalism\services\database\database;
use carlonicora\minimalism\services\encrypter\encrypter;
use carlonicora\minimalism\services\factories\servicesFactory;
use carlonicora\minimalism\services\paths\paths;
use carlonicora\minimalism\services\resourceBuilder\resourceBuilder;
use carlonicora\minimalism\services\security\security;

trait minimalismServiceTrait {
    /** @var servicesFactory */
    protected static servicesFactory $services;

    /**
     * @param servicesFactory $services
     */
    public static function initialise(servicesFactory $services) : void {
        self::$services = $services;
    }

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
     * @return resourceBuilder
     * @throws serviceNotFoundException
     */
    public static function resourceBuilder() : resourceBuilder {
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        return self::$services->service(\carlonicora\minimalism\services\resourceBuilder\factories\serviceFactory::class);
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