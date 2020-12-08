<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\Logger\LogBuilders;

use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;
use CarloNicora\Minimalism\Services\Logger\LogBuilders\InfoLogBuilder;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use function json_encode;

class InfoLogBuilderTest extends AbstractTestCase
{

    public function testEvents()
    {
        $instance = new InfoLogBuilder($this->getServices());

        self::assertEquals([], $instance->getEvents());


        $event = $this->getMockBuilder(EventInterface::class)
            ->setConstructorArgs([1, 400, 'message'])
            ->getMock();

        $event->method('generateMessage')->willReturn(json_encode([
            'time' => date('Y-m-d H:i:s'),
            'details' => 'message'
        ]));

        /** @noinspection PhpParamsInspection */
        $instance->log($event);
        self::assertEquals([$event], $instance->getEvents());
        $instance->clearEvents();
        self::assertEquals([], $instance->getEvents());


        $instance->resetEvents([$event, $event]);
        self::assertEquals([$event, $event], $instance->getEvents());


        $instance->setEvents([$event, $event]);
        self::assertEquals([$event, $event, $event, $event], $instance->getEvents());
    }
}
