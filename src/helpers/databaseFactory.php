<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\configurations;

class databaseFactory {
    /** @var configurations */
    protected static $configurations;

    public static function initialise($configurations){
        self::$configurations = $configurations;
    }

    /**
     * @param string $loaderName
     * @return databaseLoader
     */
    public static function getLoader($loaderName){
        $response = null;

        $fullName = self::$configurations->getNamespace() . '\\databases\\' . $loaderName;

        if (class_exists($fullName)){
            $response = new $fullName(self::$configurations);
        } else {
            $fullName = 'carlonicora\\minimalism\\databases\\' . $loaderName;
            if (class_exists($fullName)){
                $response = new $fullName(self::$configurations);
            } else {
                $response = new databaseLoader(self::$configurations);
            }
        }

        return($response);
    }
}