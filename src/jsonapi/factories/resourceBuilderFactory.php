<?php
namespace carlonicora\minimalism\jsonapi\factories;

use carlonicora\minimalism\interfaces\configurationsInterface;
use carlonicora\minimalism\jsonapi\interfaces\resourceBuilderInterface;

class resourceBuilderFactory {
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
        return new $objectName(self::$configurations, $data);
    }
}