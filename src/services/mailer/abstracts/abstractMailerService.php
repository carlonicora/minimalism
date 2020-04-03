<?php
namespace carlonicora\minimalism\services\mailer\abstracts;

use carlonicora\minimalism\core\services\abstracts\abstractService;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;
use carlonicora\minimalism\services\mailer\configurations\mailerConfigurations;
use carlonicora\minimalism\services\mailer\interfaces\mailerServiceInterface;
use carlonicora\minimalism\services\mailer\objects\email;

abstract class abstractMailerService extends abstractService implements mailerServiceInterface {
    /** @var mailerConfigurations  */
    protected mailerConfigurations $configData;

    /**
     * abstractMailerService constructor.
     * @param serviceConfigurationsInterface $configData
     * @param servicesFactory $services
     */
    public function __construct(serviceConfigurationsInterface $configData, servicesFactory $services){
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->configData = $configData;
    }

    /**
     * @param string $senderEmail
     * @param string $senderName
     */
    final public function setSender(string $senderEmail, string $senderName): void {
        $this->configData->senderEmail = $senderEmail;
        $this->configData->senderName = $senderName;
    }

    /**
     * @param email $email
     * @return bool
     */
    abstract public function send(email $email): bool;
}