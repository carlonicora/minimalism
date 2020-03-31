<?php
namespace carlonicora\minimalism\services\mailer\interfaces;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\services\mailer\objects\email;

interface mailerServiceInterface {
    /**
     * mailerServiceInterface constructor.
     * @param abstractConfigurations $configurations
     */
    public function __construct(abstractConfigurations $configurations);

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