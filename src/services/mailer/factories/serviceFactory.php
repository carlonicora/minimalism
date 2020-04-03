<?php
namespace carlonicora\minimalism\services\mailer\factories;

use carlonicora\minimalism\core\exceptions\configurationException;
use carlonicora\minimalism\core\services\abstracts\abstractServiceFactory;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\mailer\configurations\mailerConfigurations;
use carlonicora\minimalism\services\mailer\interfaces\mailerServiceInterface;

class serviceFactory extends abstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services) {
        $this->configData = new mailerConfigurations();

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return mailerServiceInterface
     */
    public function create(servicesFactory $services) : mailerServiceInterface {
        $mailerClass = $this->configData->getMailerClass();

        /** @var mailerServiceInterface $response */
        $response = new $mailerClass($this->configData, $services);

        return $response;
    }
}