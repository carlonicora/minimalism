<?php
namespace CarloNicora\Minimalism\Core\JsonApi\Events;

use CarloNicora\Minimalism\Core\Events\Abstracts\AbstractInfoEvent;
use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;

class JsonApiInfoEvents extends AbstractInfoEvent
{
    /** @var string  */
    protected string $serviceName = 'module-jsonapi';

    public static function TWIG_ENGINE_INITIALISED() : EventInterface
    {
        return new self(1, null, 'Twig engine initialised');
    }

    public static function PRE_RENDER_COMPLETED() : EventInterface
    {
        return new self(2, null, 'Pre render completed successfully');
    }

    public static function DATA_GENERATED() : EventInterface
    {
        return new self(3, null, 'Data generated successfully');
    }

    public static function DATA_MERGED(string $viewName) : EventInterface
    {
        return new self(4, null, 'Data merged with view %s', [$viewName]);
    }

    public static function RENDER_COMPLETE() : EventInterface
    {
        return new self(5, null, 'Render complete');
    }
}