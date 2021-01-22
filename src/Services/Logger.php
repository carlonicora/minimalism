<?php
namespace CarloNicora\Minimalism\Services;

use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use Monolog\Handler\StreamHandler;

class Logger implements ServiceInterface, LoggerInterface
{
    /**
     * Logger constructor.
     * @param Path $path
     */
    public function __construct(
        protected Path $path
    )
    {
        $this->initialise();
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function error(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->getLogger($domain)->error($message, $context);
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function warning(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->getLogger($domain)->warning($message, $context);
    }

    /**
     * @param string|null $domain
     * @return \Monolog\Logger
     */
    protected function getLogger(?string $domain=null): \Monolog\Logger
    {
        $response = new \Monolog\Logger($domain??'minimalism');
        $response->pushHandler(
            new StreamHandler(
                $this->getLogsFolder()
                . date('Ymd') . '.log',
                \Monolog\Logger::WARNING
            )
        );

        return $response;
    }

    /**
     * @return string
     */
    protected function getLogsFolder(): string
    {
        return $this->path->getRoot() . DIRECTORY_SEPARATOR
            . 'data' . DIRECTORY_SEPARATOR
            . 'logs' . DIRECTORY_SEPARATOR
            . 'minimalism' . DIRECTORY_SEPARATOR;
    }

    /**
     *
     */
    public function initialise(

    ): void
    {
    }

    /**
     *
     */
    public function destroy(): void
    {
    }
}