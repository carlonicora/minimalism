<?php
namespace carlonicora\minimalism\services\mailer\configurations;

use carlonicora\minimalism\core\exceptions\configurationException;
use carlonicora\minimalism\core\services\abstracts\abstractServiceConfigurations;

class mailerConfigurations extends abstractServiceConfigurations {
    /** @var string|null  */
    public ?string $username=null;

    /** @var string|null  */
    public ?string $password=null;

    /** @var string  */
    public string $mailerClass;

    /** @var string  */
    public ?string $senderEmail=null;

    /** @var string|null  */
    public ?string $senderName=null;

    /**
     * mailingConfigurations constructor.
     * @throws configurationException
     */
    public function __construct() {
        $this->mailerClass = '\\carlonicora\\minimalism\\services\\mailer\\modules\\' .
            (getenv('MINIMALISM_MAILING_TYPE') ?: 'mandrillapp') .
            'MailerService';

        if (!class_exists($this->mailerClass)){
            throw new configurationException('mailer', 'The selected mailer service does not exists!');
        }

        if (!getenv('MINIMALISM_MAILING_PASSWORD')) {
            throw new configurationException('mailer', 'MINIMALISM_MAILING_PASSWORD is a required configuration');
        }

        $this->username = getenv('MINIMALISM_MAILING_USERNAME');
        $this->password = getenv('MINIMALISM_MAILING_PASSWORD');

        $this->senderEmail = getenv('MINIMALISM_MAILING_SENDER_EMAIL');
        $this->senderName = getenv('MINIMALISM_MAILING_SENDER_NAME');
    }

    /**
     * @return string
     */
    public function getMailerClass() : string {
        return $this->mailerClass;
    }
}