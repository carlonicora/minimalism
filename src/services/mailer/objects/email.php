<?php
namespace carlonicora\minimalism\services\mailer\objects;

use RuntimeException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

class email {
    /** @var array  */
    public array $recipients = [];

    /** @var string  */
    public ?string $subject=null;

    /** @var string|null  */
    public ?string $body=null;

    /** @var string  */
    public string $contentType = 'text/html';

    /** @var Environment|null  */
    private ?Environment $template=null;

    /** @var string|null */
    private ?string $templateName=null;

    /**
     * email constructor.
     * @param string $subject
     * @param string|null $templateName
     * @param string|null $templateDirectory
     */
    public function __construct(string $subject, ?string $templateName=null, ?string $templateDirectory=null) {
        $this->subject = $subject;

        if ($templateName !== null) {
            $this->templateName = $templateName;

            $loader = new FilesystemLoader($templateDirectory);
            $this->template = new Environment($loader);
        }
    }

    /**
     * @param string $body
     */
    public function addBody(string $body) : void{
        $this->body = $body;
    }

    /**
     * @param string $template
     */
    public function addTemplate(string $template): void {
        $this->templateName = 'email.twig';
        $loader = new ArrayLoader([
            $this->templateName => $template
        ]);
        $this->template = new Environment($loader);
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

    /**
     * @param array $parameters
     */
    public function addParameters(array $parameters): void {
        try {
            $this->body = $this->template->render($this->templateName, $parameters);
        } catch (LoaderError $e) {
            throw new RuntimeException('Failed to create email body');
        } catch (RuntimeError $e) {
            throw new RuntimeException('Failed to create email body');
        } catch (SyntaxError $e) {
            throw new RuntimeException('Failed to create email body');
        }
    }
}