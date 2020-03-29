<?php
namespace carlonicora\minimalism\services\mailer\objects;

class email {
    /** @var array  */
    public array $recipients = [];

    /** @var string  */
    public string $subject;

    /** @var string  */
    public string $body;

    /** @var string  */
    public string $contentType = 'text/html';

    /**
     * email constructor.
     * @param string $subject
     * @param string $body
     */
    public function __construct(string $subject, string $body) {
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * @param string $email
     * @param string $name
     */
    public function addRecipient(string $email, string $name): void{
        $this->recipients[] = [
            'email' => $email,
            'name' => $name
        ];
    }
}