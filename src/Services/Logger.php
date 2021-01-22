<?php
namespace CarloNicora\Minimalism\Services;

use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use Monolog\Handler\StreamHandler;

class Logger implements ServiceInterface, LoggerInterface
{
    /** @var \Monolog\Logger */
    protected \Monolog\Logger $log;

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
     * @param array $context
     */
    public function error(
        string $message,
        array $context = []
    ): void
    {
        $this->log->error($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning(
        string $message,
        array $context = []
    ): void
    {
        $this->log->error($message, $context);
    }

    /**
     *
     */
    public function initialise(

    ): void
    {
        $this->log = new \Monolog\Logger('minimalism');
        $this->log->pushHandler(
            new StreamHandler(
                $this->path->getRoot() . DIRECTORY_SEPARATOR
                . 'data' . DIRECTORY_SEPARATOR
                . 'logs' . DIRECTORY_SEPARATOR
                . 'minimalism' . DIRECTORY_SEPARATOR
                . date('Ymd') . '.log',
                \Monolog\Logger::WARNING
            )
        );
    }

    /**
     *
     */
    public function destroy(): void
    {
    }
}