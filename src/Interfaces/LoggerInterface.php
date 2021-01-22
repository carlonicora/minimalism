<?php
namespace CarloNicora\Minimalism\Interfaces;

interface LoggerInterface
{
    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     * @return void
     */
    public function error(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     * @return void
     */
    public function warning(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void;
}