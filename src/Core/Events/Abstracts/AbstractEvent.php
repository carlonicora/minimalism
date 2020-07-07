<?php
namespace CarloNicora\Minimalism\Core\Events\Abstracts;

use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Services\Exceptions\MinimalismHttpException;
use Exception;

abstract class AbstractEvent implements EventInterface
{
    /** @var string */
    protected string $serviceName = '';

    /** @var int */
    protected int $id;

    /** @var string|null */
    protected ?string $httpStatusCode = null;

    /** @var int|null */
    protected ?int $errorUniqueCode = null;

    /** @var string */
    protected ?string $message = null;

    /** @var Exception|null */
    protected ?Exception $e = null;

    /** @var float */
    private float $time;
    /**
     * AbstractEvent constructor.
     * @param int $id
     * @param string|null $httpStatusCode
     * @param string $message
     * @param array $context
     * @param Exception|null $e
     */
    public function __construct(int $id, ?string $httpStatusCode, string $message, array $context=[], Exception $e=null)
    {
        $this->message = $this->mergeContext($message, $context);
        $this->id = $id;
        $this->httpStatusCode = $httpStatusCode;
        $this->e = $e;
        $this->time = microtime(true);
    }

    /**
     * @return string
     */
    final public function getService(): string
    {
        return $this->serviceName;
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getMessageCode(): string
    {
        return (string)$this->id;
    }

    /**
     * @return string
     */
    public function getHttpStatusCode(): string
    {
        return $this->httpStatusCode ?? ResponseInterface::HTTP_STATUS_500;
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    private function mergeContext(?string $message, ?array $context) : ?string
    {
        if ($message !== '' && count($context) > 0) {
            $message = sprintf($message, ...$context);
        }

        return $message;
    }

    /**
     * @return string
     */
    abstract public function generateMessage(): string;

    /**
     * @param string $exceptionName
     * @param string|null $message
     * @throws Exception
     */
    public function throw(string $exceptionName=Exception::class, ?string $message = null): void
    {
        throw $this->generateException($exceptionName, $message);
    }

    /**
     * @param string $exceptionName
     * @param string|null $message
     * @return Exception
     */
    public function generateException(string $exceptionName=Exception::class, ?string $message = null): Exception
    {
        if ($exceptionName === MinimalismHttpException::class) {
            return new MinimalismHttpException(
                $message ?? $this->message,
                $this->getMessageCode(),
                $this->getHttpStatusCode(),
                $this->e);
        }

        return new $exceptionName(
            $message ?? $this->message,
            $this->getHttpStatusCode(),
            $this->e);
}
}
