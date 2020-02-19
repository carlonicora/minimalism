<?php
namespace carlonicora\minimalism\factories;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\helpers\idEncrypter;

class encrypterFactory {
    /** @var abstractConfigurations */
    private static $configurations;

    /** @var idEncrypter */
    private static $idEncrypter;

    /**
     * @param abstractConfigurations $configurations
     */
    public function initialise(abstractConfigurations $configurations): void {
        self::$configurations = $configurations;
    }

    /**
     * @return idEncrypter
     */
    public function encrypter(): idEncrypter {
        if (self::$idEncrypter === null){
            self::$idEncrypter = new idEncrypter(self::$configurations);
        }

        return self::$idEncrypter;
    }
}