<?php
namespace CarloNicora\Minimalism\Exceptions;

use CarloNicora\Minimalism\Enums\HttpCode;
use Exception;

class MinimalismException extends Exception
{
    /** @var int  */
    private int $id;

    /**
     * @param HttpCode $status
     * @param string $message
     * @param int $code
     */
    public function __construct(
        private HttpCode $status,
        string $message = '',
        int $code = 0,
    )
    {
        parent::__construct($message, $code);

        try {
            $this->id = (int)(microtime(true) * 1000000) + random_int(0, 999);
        } catch (Exception) {
            $this->id = (int)(microtime(true) * 1000000);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return HttpCode
     */
    public function getStatus(
    ): HttpCode
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getHttpCode(
    ): string
    {
        return (string)$this->status->value;
    }
}