<?php
namespace CarloNicora\Minimalism\Interfaces;

interface LoggerInterface
{
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function error(string $message, array $context = []): void;

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function warning(string $message, array $context = []): void;
}