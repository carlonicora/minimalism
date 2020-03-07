<?php
namespace carlonicora\minimalism\factories;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\helpers\idEncrypter;

class encrypterFactory {
    /** @var abstractConfigurations */
    private static abstractConfigurations $configurations;

    /** @var idEncrypter */
    private static idEncrypter $idEncrypter;

    /**
     * @param abstractConfigurations $configurations
     */
    public static function initialise(abstractConfigurations $configurations): void {
        self::$configurations = $configurations;
    }

    /**
     * @return idEncrypter
     */
    public static function encrypter(): idEncrypter {
        if (self::$idEncrypter === null){
            self::$idEncrypter = new idEncrypter(self::$configurations);
        }

        return self::$idEncrypter;
    }
}