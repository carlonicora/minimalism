<?php
namespace carlonicora\minimalism\services\mailer\interfaces;

use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\mailer\configurations\mailerConfigurations;
use carlonicora\minimalism\services\mailer\objects\email;

interface mailerServiceInterface {
    /**
     * mailerServiceInterface constructor.
     * @param mailerConfigurations $configurations
     * @param servicesFactory $services
     */
    public function __construct(mailerConfigurations $configurations, servicesFactory $services);

    /**
     * @param string $senderEmail
     * @param string $senderName
     */
    public function setSender(string $senderEmail, string $senderName):void;

    /**
     * @param email $email
     * @return bool
     */
    public function send(email $email):bool;
}