<?php
namespace CarloNicora\Minimalism\Objects;

use Throwable;

class MinimalismLog
{
    /**
     * MinimalismLog constructor.
     * @param int $level
     * @param string|null $domain
     * @param string $message
     * @param array|null $context
     * @param Throwable|null $exception
     */
    public function __construct(
        private int $level,
        private ?string $domain,
        private string $message,
        private ?array $context=null,
        private ?Throwable $exception = null
    ) {
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array|null
     */
    public function getContext(): ?array
    {
        if (isset($this->exception)) {
            $this->context['exception'] = $this->exception;
        }

        return $this->context;
    }

    /**
     * @return Throwable|null
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    /**
     * @param string $request
     */
    public function addUriToContext(string $request): void
    {
        if ($this->context === null){
            $this->context = [];
        }

        $this->context['uri'] = $request;
    }
}