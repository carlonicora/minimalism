<?php
namespace CarloNicora\Minimalism\Objects;

class MinimalismLog
{
    /**
     * MinimalismLog constructor.
     * @param int $level
     * @param string $message
     * @param string|null $domain
     * @param array|null $context
     */
    public function __construct(
        private int $level,
        private ?string $domain,
        private string $message,
        private ?array $context=null
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
        return $this->context;
    }
}