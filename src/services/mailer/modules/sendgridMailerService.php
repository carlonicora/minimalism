<?php
namespace carlonicora\minimalism\services\mailer\modules;

use carlonicora\minimalism\services\mailer\abstracts\abstractMailerService;
use carlonicora\minimalism\services\mailer\objects\email;
use Exception;
use RuntimeException;
use SendGrid;
use SendGrid\Mail\Mail;

class sendgridMailerService extends abstractMailerService {
    /**
     * @param email $email
     * @return bool
     * @throws Exception
     */
    public function send(email $email): bool {
        $sendGridEmail = new Mail();

        try {
            $sendGridEmail->setFrom($this->configData->senderEmail, $this->configData->senderName);
        } catch (SendGrid\Mail\TypeException $e) {
            throw new RuntimeException($e->getMessage());
        }

        foreach ($email->recipients as $recipient) {
            $sendGridEmail->addTo($recipient['email'], $recipient['name']);
        }

        try {
            $sendGridEmail->setSubject($email->subject);
        } catch (SendGrid\Mail\TypeException $e) {
            throw new RuntimeException($e->getMessage());
        }

        $sendGridEmail->addContent($email->contentType, $email->body);

        $sendgrid = new SendGrid($this->configData->password);
        $sendgrid->send($sendGridEmail);

        return true;
    }
}