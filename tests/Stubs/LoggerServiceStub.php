<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\Minimalism\Enums\LogLevel;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Services\Path;

class LoggerServiceStub implements LoggerInterface
{

    public function __construct(Path $path, int $MINIMALISM_LOG_LEVEL = 300)
    {
    }

    public function getLogLevel(): LogLevel
    {
        return LogLevel::Error;
    }

    public function addExtraInformation(string $name, int|string $value,): void
    {
        // TODO: Implement addExtraInformation() method.
    }

    public function debug(string $message, ?string $domain = null, array $context = []): void
    {
        // TODO: Implement debug() method.
    }

    public function info(string $message, ?string $domain = null, array $context = []): void
    {
        // TODO: Implement info() method.
    }

    public function notice(string $message, ?string $domain = null, array $context = []): void
    {
        // TODO: Implement notice() method.
    }

    public function warning(string $message, ?string $domain = null, array $context = []): void
    {
        // TODO: Implement warning() method.
    }

    public function error(string $message, ?string $domain = null, array $context = []): void
    {
        // TODO: Implement error() method.
    }

    public function critical(string $message, ?string $domain = null, array $context = []): void
    {
        // TODO: Implement critical() method.
    }

    public function alert(string $message, ?string $domain = null, array $context = []): void
    {
        // TODO: Implement alert() method.
    }

    public function emergency(string $message, ?string $domain = null, array $context = []): void
    {
        // TODO: Implement emergency() method.
    }

    public function flush(): void
    {
        // TODO: Implement flush() method.
    }

    public function initialise(): void
    {
        // TODO: Implement initialise() method.
    }

    public function postIntialise(ServiceFactory $services,): void
    {
        // TODO: Implement postIntialise() method.
    }

    public function destroy(): void
    {
        // TODO: Implement destroy() method.
    }

    public function setObjectFactory(ObjectFactory $objectFactory): void
    {
        // TODO: Implement setObjectFactory() method.
    }

    public function unsetObjectFactory(): void
    {
        // TODO: Implement unsetObjectFactory() method.
    }

    public static function getBaseInterface(): ?string
    {
        return 'baseLoggerInterface';
    }
}