<?php
namespace CarloNicora\Minimalism\Services;

use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Objects\MinimalismLog;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\Handler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class MinimalismLogger implements ServiceInterface, LoggerInterface
{
    /**
     * @var array
     */
    protected array $extra=[];

    /**
     * @var array
     */
    private array $handlers=[];

    /** @var array|MinimalismLog[]  */
    private array $logs=[];

    /**
     * Logger constructor.
     * @param Path $path
     * @param int $MINIMALISM_LOG_LEVEL
     */
    public function __construct(
        private Path $path,
        private int $MINIMALISM_LOG_LEVEL= Logger::WARNING
    )
    {
        $this->handlers[] = [$this, 'getStreamHandler'];
        $this->initialise();
    }

    /**
     * @param callable $handler
     */
    protected function addHandler(callable $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param string $name
     * @param string|int $value
     */
    public function addExtraInformation(string $name, string|int $value): void
    {
        $this->extra[$name] = $value;
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function debug(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new MinimalismLog(
            Logger::DEBUG,
            $domain,
            $message,
            $context
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function info(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new MinimalismLog(
            Logger::INFO,
            $domain,
            $message,
            $context
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function notice(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new MinimalismLog(
            Logger::NOTICE,
            $domain,
            $message,
            $context
        );
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
        $this->logs[] = new MinimalismLog(
            Logger::WARNING,
            $domain,
            $message,
            $context
        );
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
        $this->logs[] = new MinimalismLog(
            Logger::ERROR,
            $domain,
            $message,
            $context
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function critical(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new MinimalismLog(
            Logger::CRITICAL,
            $domain,
            $message,
            $context
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function alert(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new MinimalismLog(
            Logger::ALERT,
            $domain,
            $message,
            $context
        );
    }

    /**
     * @param string $message
     * @param string|null $domain
     * @param array $context
     */
    public function emergency(
        string $message,
        ?string $domain=null,
        array $context = []
    ): void
    {
        $this->logs[] = new MinimalismLog(
            Logger::EMERGENCY,
            $domain,
            $message,
            $context
        );
    }

    /**
     * @param string|null $domain
     * @return Logger
     */
    protected function getLogger(?string $domain=null): Logger
    {
        $response = new Logger($domain??'minimalism');
        $this->setHandlers($response);

        $response->pushProcessor(function($record){
            foreach ($this->extra as $name=>$value){
                $record['extra'][$name] = $value;
            }
            return $record;
        });

        return $response;
    }

    /**
     * @param Logger $logger
     */
    protected function setHandlers(Logger $logger): void
    {
        foreach ($this->handlers ?? [] as $handler){
            $logger->pushHandler(
                $handler()
            );
        }
    }

    /**
     * @return Handler
     */
    protected function getStreamHandler(): Handler
    {
        $response = new StreamHandler(
            $this->getLogsFolder()
            . date('Ymd') . '.log',
            $this->MINIMALISM_LOG_LEVEL
        );
        $response->setFormatter(new JsonFormatter());

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
        foreach ($this->logs ?? [] as $log){
            switch ($log->getLevel()){
                case Logger::INFO:
                    $this->getLogger($log->getDomain())->info($log->getMessage(), $log->getContext());
                    break;
                case Logger::NOTICE:
                    $this->getLogger($log->getDomain())->notice($log->getMessage(), $log->getContext());
                    break;
                case Logger::WARNING:
                    $this->getLogger($log->getDomain())->warning($log->getMessage(), $log->getContext());
                    break;
                case Logger::ERROR:
                    $this->getLogger($log->getDomain())->error($log->getMessage(), $log->getContext());
                    break;
                case Logger::CRITICAL:
                    $this->getLogger($log->getDomain())->critical($log->getMessage(), $log->getContext());
                    break;
                case Logger::ALERT:
                    $this->getLogger($log->getDomain())->alert($log->getMessage(), $log->getContext());
                    break;
                case Logger::EMERGENCY:
                    $this->getLogger($log->getDomain())->emergency($log->getMessage(), $log->getContext());
                    break;
            }
        }

        $this->logs = [];
    }
}