<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\Logger\LogBuilders;

use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;
use CarloNicora\Minimalism\Services\Logger\LogBuilders\InfoLogBuilder;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class InfoLogBuilderTest extends AbstractTestCase
{

    public function testEvents()
    {
        $instance = new InfoLogBuilder($this->getServices());

        $this->assertEquals([], $instance->getEvents());


        $event = $this->getMockBuilder(EventInterface::class)
            ->setConstructorArgs([1, 400, 'message'])
            ->getMock();

        $event->method('generateMessage')->willReturn(\json_encode([
            'time' => date('Y-m-d H:i:s'),
            'details' => 'message'
        ]));

        $instance->log($event);
        $this->assertEquals([$event], $instance->getEvents());
        $instance->clearEvents();
        $this->assertEquals([], $instance->getEvents());


        $instance->resetEvents([$event, $event]);
        $this->assertEquals([$event, $event], $instance->getEvents());


        $instance->setEvents([$event, $event]);
        $this->assertEquals([$event, $event, $event, $event], $instance->getEvents());
    }
}
