<?php
namespace carlonicora\minimalism\factories;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\helpers\idEncrypter;
use carlonicora\minimalism\helpers\stringEncrypter;

class encrypterFactory {
    /** @var abstractConfigurations */
    private static abstractConfigurations $configurations;

    /** @var idEncrypter|null */
    private static ?idEncrypter $idEncrypter=null;

    /** @var stringEncrypter|null */
    private static ?stringEncrypter $stringEncrypter=null;

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

    /**
     * @return stringEncrypter
     */
    public static function stringEncrypter(): stringEncrypter {
        if (self::$stringEncrypter === null){
            self::$stringEncrypter = new stringEncrypter();
        }

        return self::$stringEncrypter;
    }
}