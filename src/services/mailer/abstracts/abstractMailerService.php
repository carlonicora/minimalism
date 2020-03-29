<?php
namespace carlonicora\minimalism\services\mailer\abstracts;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\services\mailer\interfaces\mailerServiceInterface;
use carlonicora\minimalism\services\mailer\objects\email;

abstract class abstractMailerService implements mailerServiceInterface {
    /** @var string|null  */
    protected ?string $senderEmail=null;

    /** @var string|null  */
    protected ?string $senderName=null;

    /** @var string  */
    protected string $username;

    /** @var string  */
    protected string $password;

    /**
     * abstractMailerService constructor.
     * @param abstractConfigurations $configurations
     */
    public function __construct(abstractConfigurations $configurations){
        $this->username = $configurations->configData()->mailer()->username;
        $this->password = $configurations->configData()->mailer()->password;
        $this->senderName = $configurations->configData()->mailer()->senderName;
        $this->senderEmail = $configurations->configData()->mailer()->senderEmail;
    }

    /**
     * @param string $senderEmail
     * @param string $senderName
     */
    final public function setSender(string $senderEmail, string $senderName): void {
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    /**
     * @param email $email
     * @return bool
     */
    abstract public function send(email $email): bool;
}