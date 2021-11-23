<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\Minimalism\Enums\LogLevel;
use CarloNicora\Minimalism\Services\Path;

interface LoggerInterface
{
    /**
     * LoggerInterface constructor.
     * @param Path $path
     * @param int $MINIMALISM_LOG_LEVEL
     */
    public function __construct(
        Path $path,
        int $MINIMALISM_LOG_LEVEL=300,
    );

    /**
     * @return LogLevel
     */
    public function getLogLevel(
    ): LogLevel;

    /**
     * @param string $name
     * @param string|int $value
     */
    public function addExtraInformation(
        string $name,
        string|int $value,
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function debug(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function info(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function notice(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function warning(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
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
     */
    public function critical(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function alert(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function emergency(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void;

    /**
     *
     */
    public function flush(): void;
}