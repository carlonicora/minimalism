<?php
namespace CarloNicora\Minimalism\Core\Services\Exceptions;

use Exception;
use Throwable;

class MinimalismHttpException extends Exception
{
    protected ?int $httpStatusCode = null;

    /**
     * MinimalismHttpException constructor.
     * @param string $message
     * @param int $code
     * @param int|null $httpStatusCode
     * @param Throwable|null $previous
     */
    public function __construct(string $message, int $code, ?int $httpStatusCode = null, Throwable $previous = null)
    {
        $this->httpStatusCode = $httpStatusCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int|null
     */
    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }
}