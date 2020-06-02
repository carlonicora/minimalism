<?php
namespace CarloNicora\Minimalism\Core\Events;

use CarloNicora\Minimalism\Core\Events\Abstracts\AbstractInfoEvent;
use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;

class MinimalismInfoEvents extends AbstractInfoEvent
{
    /** @var string  */
    protected string $serviceName = 'minimalism';

    public static function START() : EventInterface
    {
        $context = [''];
        if (array_key_exists('REQUEST_URI', $_SERVER)){
            $context[0] = $_SERVER['REQUEST_URI'];
        }
        return new self(1, null, 'Request initiated %s', $context);
    }

    public static function END() : EventInterface
    {
        return new self(2, null, 'Request completed');
    }

    public static function SERVICES_LOADED_FROM_SESSION() : EventInterface
    {
        return new self(3, null, 'Services loaded from session');
    }

    public static function SERVICES_LOADED_FROM_CACHE() : EventInterface
    {
        return new self(4, null, 'Services loaded from cache');
    }

    public static function SERVICES_INITIALISED() : EventInterface
    {
        return new self(5, null, 'Services initialised from scratch');
    }

    public static function PARAMETERS_INITIALISED() : EventInterface
    {
        return new self(6, null, 'Parameters initialised');
    }

    public static function PARAMETERS_VALIDATED() : EventInterface
    {
        return new self(7, null, 'Parameters validated');
    }

    public static function MODEL_INITIALISED(string $modelName) : EventInterface
    {
        return new self(8, null, 'Model ' . $modelName . ' initialised');
    }

    public static function SESSION_PERSISTED() : EventInterface
    {
        return new self(8, null, 'Session Persisted');
    }

    public static function CONTROLLER_INITIALISED() : EventInterface
    {
        return new self(9, null, 'Controller intialised');
    }

    public static function MODEL_RUN(string $verb = null) : EventInterface
    {
        return new self(10, null, 'Model run successfully' . ($verb !== null ? '(' . $verb . ')' : ''));
    }

    public static function SECURITY_CHECK_PASSED() : EventInterface
    {
        return new self(11, null, 'Request security check passed successfully');
    }
}