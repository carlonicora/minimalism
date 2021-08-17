<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\Minimalism\Services\Path;
use Monolog\Logger;
use Throwable;

interface LoggerInterface
{
    /**
     * LoggerInterface constructor.
     * @param Path $path
     * @param int $MINIMALISM_LOG_LEVEL
     */
    public function __construct(
        Path $path,
        int $MINIMALISM_LOG_LEVEL=Logger::WARNING
    );

    /**
     * @param string $name
     * @param string|int $value
     */
    public function addExtraInformation(string $name, string|int $value): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     * @param Throwable|null $exception
     */
    public function debug(
        string $message,
        ?string $domain=null,
        array $context = [],
        Throwable $exception = null
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     * @param Throwable|null $exception
     */
    public function info(
        string $message,
        ?string $domain=null,
        array $context = [],
        Throwable $exception = null
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     * @param Throwable|null $exception
     */
    public function notice(
        string $message,
        ?string $domain=null,
        array $context = [],
        Throwable $exception = null
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     * @param Throwable|null $exception
     */
    public function warning(
        string $message,
        ?string $domain=null,
        array $context = [],
        Throwable $exception = null
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     * @param Throwable|null $exception
     */
    public function error(
        string $message,
        ?string $domain=null,
        array $context = [],
        Throwable $exception = null
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     * @param Throwable|null $exception
     */
    public function critical(
        string $message,
        ?string $domain=null,
        array $context = [],
        Throwable $exception = null
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     * @param Throwable|null $exception
     */
    public function alert(
        string $message,
        ?string $domain=null,
        array $context = [],
        Throwable $exception = null
    ): void;

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     * @param Throwable|null $exception
     */
    public function emergency(
        string $message,
        ?string $domain=null,
        array $context = [],
        Throwable $exception = null
    ): void;

    /**
     *
     */
    public function flush(): void;
}