<?php
namespace carlonicora\minimalism\businessObjects;

use carlonicora\minimalism\businessObjects\interfaces\businessObjectsArrayInterface;
use carlonicora\minimalism\businessObjects\interfaces\businessObjectsInterface;
use carlonicora\minimalism\interfaces\configurationsInterface;

class minimalismBo {
    /** @var array */
    private static array $businessObjects=[];

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
     * @return businessObjectsInterface
     */
    public static function businessObject(string $objectName) : businessObjectsInterface {
        if (!array_key_exists($objectName,  self::$businessObjects)) {
            self::$businessObjects[$objectName] = new $objectName();
        }

        return self::$businessObjects[$objectName];
    }

    /**
     * @param string $objectName
     * @return businessObjectsArrayInterface
     */
    public static function businessObjectArray(string $objectName): businessObjectsArrayInterface {
        if (!array_key_exists($objectName,  self::$businessObjects)) {
            $singleBusinessObject = self::businessObject(substr($objectName, 0, -5));
            self::$businessObjects[$objectName] = new $objectName($singleBusinessObject);
        }

        return self::$businessObjects[$objectName];
    }
}