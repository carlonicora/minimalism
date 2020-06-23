<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Events\Abstracts;

use CarloNicora\Minimalism\Core\Events\Abstracts\AbstractEvent;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class AbstractEventTest extends AbstractTestCase
{

    public function testPublicMethodsWithNoData(): void
    {
        $mock = $this->getMockBuilder(AbstractEvent::class)
            ->setConstructorArgs([
                0, // id
                null, // http status code
                '', // message
                [], // context
                null // exception
            ])
            ->getMockForAbstractClass();

        $this->assertEquals('0', $mock->getMessageCode());
        $this->assertEquals(ResponseInterface::HTTP_STATUS_500, $mock->getHttpStatusCode());
    }

    public function testPublicMethodsWithData(): void
    {
        $mock = $this->getMockBuilder(AbstractEvent::class)
            ->setConstructorArgs([
                1, // id
                200, // http status code
                'test', // message
                ['message', 'context'], // context
                null // exception
            ])
            ->getMockForAbstractClass();

        $this->assertEquals('1', $mock->getMessageCode());
    }
}
