<?php
namespace CarloNicora\Minimalism\Services\Logger\Events;

use CarloNicora\Minimalism\Services\Logger\Interfaces\LogMessageInterface;
use CarloNicora\Minimalism\Services\Logger\LogMessages\ErrorLogMessage;
use Throwable;

class MinimalismErrorEvents extends ErrorLogMessage
{
    protected string $serviceName = 'minimalism';

    public static function GENERIC_ERROR(Throwable $e=null) : LogMessageInterface
    {
        return new self(1, 'Exception Generated', [], $e);
    }
}