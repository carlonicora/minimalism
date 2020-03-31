<?php
namespace carlonicora\minimalism\factories;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\services\mailer\interfaces\mailerServiceInterface;

class minimalismFactory {
    /** @var abstractConfigurations  */
    private static ?abstractConfigurations $configurations=null;

    /** @var mailerServiceInterface|null */
    private static ?mailerServiceInterface $mailerService=null;

    /**
     * @param abstractConfigurations $configurations
     */
    public static function initialise(abstractConfigurations $configurations) : void {
        self::$configurations = $configurations;
    }

    /**
     * @return mailerServiceInterface
     */
    public static function mailer():mailerServiceInterface {
        if (self::$mailerService === null) {
            $mailerClass = self::$configurations->configData()->mailer()->mailerClass;
            self::$mailerService = new $mailerClass(self::$configurations);
        }

        return self::$mailerService;
    }
}