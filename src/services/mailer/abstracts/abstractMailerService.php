<?php
namespace carlonicora\minimalism\services\mailer\abstracts;

use carlonicora\minimalism\services\abstracts\abstractService;
use carlonicora\minimalism\services\mailer\configurations\mailerConfigurations;
use carlonicora\minimalism\services\mailer\interfaces\mailerServiceInterface;
use carlonicora\minimalism\services\mailer\objects\email;

abstract class abstractMailerService extends abstractService implements mailerServiceInterface {
    /** @var mailerConfigurations  */
    protected mailerConfigurations $configData;

    /**
     * abstractMailerService constructor.
     * @param mailerConfigurations $configurations
     */
    public function __construct(mailerConfigurations $configurations){
        $this->configData = $configurations;
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