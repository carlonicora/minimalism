<?php
namespace CarloNicora\Minimalism\Services\Logger\Interfaces;

use Throwable;

interface LogMessageInterface
{
    /**
     * LogMessageInterface constructor.
     * @param int $id
     * @param string $message
     * @param array $context
     * @param Throwable|null $e
     */
    public function __construct(int $id, string $message, array $context=[], Throwable $e=null);

    /**
     * @return string
     */
    public function generateMessage() : string;

    /**
     * @return string
     */
    public function getService() : string;

    /**
     * @return float
     */
    public function getTime() : float;

    /**
     * @return string
     */
    public function getMessageCode() : string;

    /**
     * @param string $exceptionName
     * @param string|null $message
     * @throws Throwable
     */
    public function throw(string $exceptionName, ?string $message=null) : void;
}