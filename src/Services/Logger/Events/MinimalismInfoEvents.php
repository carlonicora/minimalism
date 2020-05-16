<?php
namespace CarloNicora\Minimalism\Services\Logger\Events;

use CarloNicora\Minimalism\Services\Logger\Interfaces\LogMessageInterface;
use CarloNicora\Minimalism\Services\Logger\LogMessages\InfoLogMessage;

class MinimalismInfoEvents extends InfoLogMessage
{
    /** @var string  */
    protected string $serviceName = 'minimalism';

    public static function START() : LogMessageInterface
    {
        $context = [''];
        if (array_key_exists('REQUEST_URI', $_SERVER)){
            $context[0] = $_SERVER['REQUEST_URI'];
        }
        return new self(1, 'Request initiated %s', $context);
    }

    public static function END() : LogMessageInterface
    {
        return new self(2, 'Request completed');
    }

    public static function SERVICES_LOADED_FROM_SESSION() : LogMessageInterface
    {
        return new self(3, 'Services loaded from session');
    }

    public static function SERVICES_LOADED_FROM_CACHE() : LogMessageInterface
    {
        return new self(4, 'Services loaded from cache');
    }

    public static function SERVICES_INITIALISED() : LogMessageInterface
    {
        return new self(5, 'Services initialised from scratch');
    }

    public static function PARAMETERS_INITIALISED() : LogMessageInterface
    {
        return new self(6, 'Parameters initialised');
    }

    public static function PARAMETERS_VALIDATED() : LogMessageInterface
    {
        return new self(7, 'Parameters validated');
    }

    public static function MODEL_INITIALISED(string $modelName) : LogMessageInterface
    {
        return new self(8, 'Model ' . $modelName . ' initialised');
    }

    public static function SESSION_PERSISTED() : LogMessageInterface
    {
        return new self(8, 'Session Persisted');
    }
}