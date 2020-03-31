<?php
namespace carlonicora\minimalism\services\mailer\factories;

use carlonicora\minimalism\exceptions\configurationException;
use carlonicora\minimalism\services\interfaces\serviceFactoryInterface;
use carlonicora\minimalism\services\mailer\configurations\mailerConfigurations;
use carlonicora\minimalism\services\mailer\interfaces\mailerServiceInterface;

class serviceFactory implements serviceFactoryInterface {
    /** @var mailerConfigurations  */
    private mailerConfigurations $configData;

    /**
     * abstractMailerService constructor.
     * @throws configurationException
     */
    public function __construct() {
        $this->configData = new mailerConfigurations();
    }

    /**
     * @return mailerServiceInterface
     */
    public function create() : mailerServiceInterface {
        $mailerClass = $this->configData->getMailerClass();

        /** @var mailerServiceInterface $response */
        $response = new $mailerClass($this->configData);

        return $response;
    }
}