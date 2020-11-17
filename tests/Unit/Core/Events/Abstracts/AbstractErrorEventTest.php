<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Events\Abstracts;

use CarloNicora\Minimalism\Core\Events\Abstracts\AbstractErrorEvent;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use function strtotime;

class AbstractErrorEventTest extends AbstractTestCase
{

    /**
     * @param string $message
     * @param Exception|null $exception
     * @return MockObject
     */
    protected function mockInstanceWith($message = '', Exception $exception = null) {
        $mock = $this->getMockBuilder(AbstractErrorEvent::class)
            ->setConstructorArgs([
                1, // id
                null, // http status code
                $message,
                [], // context
                $exception
            ])
            ->onlyMethods(['getTime'])
            ->getMockForAbstractClass();

        $mock->expects($this->once())->method('getTime')->willReturn((float)strtotime('2020-06-03 12:00:00'));

        return $mock;
    }

    public function testGenerateMessageWithoutExceptionOrMessage()
    {
        $mock = $this->mockInstanceWith('', null);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(
            '{"time":"2020-06-03 12:00:00","service":"","id":1,"error":""}',
            $mock->generateMessage()
        );
    }

    public function testGenerateMessageWithMessage() {
        $mock = $this->mockInstanceWith('test message', null);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(
            '{"time":"2020-06-03 12:00:00","service":"","id":1,"error":"test message"}',
            $mock->generateMessage()
        );
    }

    /*
    public function testGenerateMessageWithException() {
        $mock = $this->mockInstanceWith('', new Exception('some failure'));
        $this->markTestSkipped('Stack trace has absolute paths to within the filesystem.');

        $this->assertEquals(
            '',
            $mock->generateMessage()
        );
    }
    */
}
