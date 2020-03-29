<?php
namespace carlonicora\minimalism\services\mailer\configurations;

use RuntimeException;

class mailerConfigurations {
    /** @var string|null  */
    public ?string $username=null;

    /** @var string|null  */
    public ?string $password=null;

    /** @var string  */
    public string $mailerClass;

    /**
     * mailingConfigurations constructor.
     */
    public function __construct() {
        if (getenv('MINIMALISM_MAILING_TYPE') !== null){
            if (class_exists(getenv('MINIMALISM_MAILING_TYPE'))){
                $this->mailerClass = getenv('MINIMALISM_MAILING_TYPE');
            } else {
                $this->mailerClass = 'carlonicora\\minimalism\\services\\mailer\\modules\\' .
                    (getenv('MINIMALISM_MAILING_TYPE') ?? 'mandrillapp') .
                    'MailerService';

                if (!class_exists($this->mailerClass)){
                    throw new RuntimeException('The selected mailer service does not exists!');
                }
            }
        }

        $this->username = getenv('MINIMALISM_MAILING_USERNAME');
        $this->password = getenv('MINIMALISM_MAILING_PASSWORD');
    }
}