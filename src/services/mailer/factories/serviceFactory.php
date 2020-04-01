<?php
namespace carlonicora\minimalism\services\mailer\factories;

use carlonicora\minimalism\core\exceptions\configurationException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceFactoryInterface;
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
     * @param servicesFactory $services
     * @return mailerServiceInterface
     */
    public function create(servicesFactory $services) : mailerServiceInterface {
        $mailerClass = $this->configData->getMailerClass();

        /** @var mailerServiceInterface $response */
        $response = new $mailerClass($this->configData);

        return $response;
    }
}