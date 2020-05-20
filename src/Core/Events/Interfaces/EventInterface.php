<?php
namespace CarloNicora\Minimalism\Core\Events\Interfaces;

use Exception;

interface EventInterface
{
    /**
     * LogMessageInterface constructor.
     * @param int $id
     * @param string|null $httpStatusCode
     * @param string $message
     * @param array $context
     * @param Exception|null $e
     */
    public function __construct(int $id, ?string $httpStatusCode, string $message, array $context=[], Exception $e=null);

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
     * @return string
     */
    public function getHttpStatusCode() : string;

    /**
     * @param string $exceptionName
     * @param string|null $message
     * @throws Exception
     */
    public function throw(string $exceptionName=Exception::class, ?string $message=null) : void;

    /**
     * @param string $exceptionName
     * @param string|null $message
     * @return Exception
     */
    public function generateException(string $exceptionName=Exception::class, ?string $message=null) : Exception;
}