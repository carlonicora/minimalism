<?php
namespace CarloNicora\Minimalism\Core\Events;

use CarloNicora\Minimalism\Core\Events\Abstracts\AbstractErrorEvent;
use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use Exception;

class MinimalismErrorEvents extends AbstractErrorEvent
{
    protected string $serviceName = 'minimalism';

    public static function GENERIC_ERROR(Exception $e=null) : EventInterface
    {
        return new self(1, ResponseInterface::HTTP_STATUS_500, 'Exception Generated', [], $e);
    }

    public static function SERVICE_CACHE_ERROR(Exception $e) : EventInterface
    {
        return new self(2, ResponseInterface::HTTP_STATUS_500, 'Services could not be cached', [], $e);
    }

    public static function COOKIE_SETTING_ERROR(Exception $e) : EventInterface
    {
        return new self(3, ResponseInterface::HTTP_STATUS_500, 'Services could not be saved in the cookies', [], $e);
    }
 }