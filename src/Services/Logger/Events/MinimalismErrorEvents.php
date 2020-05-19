<?php
namespace CarloNicora\Minimalism\Services\Logger\Events;

use CarloNicora\Minimalism\Services\Logger\Interfaces\LogMessageInterface;
use CarloNicora\Minimalism\Services\Logger\LogMessages\ErrorLogMessage;
use Exception;

class MinimalismErrorEvents extends ErrorLogMessage
{
    protected string $serviceName = 'minimalism';

    public static function GENERIC_ERROR(Exception $e=null) : LogMessageInterface
    {
        return new self(1, 'Exception Generated', [], $e);
    }

    public static function SERVICE_CACHE_ERROR(Exception $e) : LogMessageInterface
    {
        return new self(2, 'Services could not be cached', [], $e);
    }

    public static function COOKIE_SETTING_ERROR(Exception $e) : LogMessageInterface
    {
        return new self(3, 'Services could not be saved in the cookies', [], $e);
    }
 }