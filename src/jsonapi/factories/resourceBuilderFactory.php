<?php
namespace carlonicora\minimalism\jsonapi\factories;

use carlonicora\minimalism\interfaces\configurationsInterface;
use carlonicora\minimalism\jsonapi\interfaces\resourceBuilderInterface;

class resourceBuilderFactory {
    /** @var array */
    private static array $resourceBuilders=[];

    /** @var configurationsInterface|null  */
    private static ?configurationsInterface $configurations=null;

    /**
     * @param configurationsInterface $configurations
     */
    public static function initialise(configurationsInterface $configurations) : void {
        self::$configurations = $configurations;
    }

    /**
     * @param string $objectName
     * @param array $data
     * @return resourceBuilderInterface
     */
    public static function resourceBuilder(string $objectName, array $data) : resourceBuilderInterface {
        if (!array_key_exists($objectName,  self::$resourceBuilders)) {
            self::$resourceBuilders[$objectName] = new $objectName(self::$configurations, $data);
        }

        return self::$resourceBuilders[$objectName];
    }
}