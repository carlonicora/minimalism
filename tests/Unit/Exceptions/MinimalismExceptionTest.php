<?php

namespace CarloNicora\Minimalism\Tests\Unit\Exceptions;

use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Exceptions\MinimalismException;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;

/**
 * Class MinimalismExceptionTest
 * @package CarloNicora\Minimalism\Tests\Unit\Exceptions
 * @coversDefaultClass \CarloNicora\Minimalism\Exceptions\MinimalismException
 */
class MinimalismExceptionTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getId
     * @return void
     */
    public function testItShouldGetId(
    ): void
    {
        $exception = new MinimalismException(HttpCode::BadGateway);

        $this->assertIsInt($exception->getId());
    }

    /**
     * @covers ::getStatus
     * @return void
     */
    public function testItShouldGetStatus(
    ): void
    {
        $status = HttpCode::TemporaryRedirect;
        $exception = new MinimalismException($status);

        $this->assertSame(
            expected: $status,
            actual: $exception->getStatus()
        );
    }

    /**
     * @covers ::getHttpCode
     * @return void
     */
    public function testItShouldGetHttpCode(
    ): void
    {
        $status = HttpCode::NonAuthoritativeInformation;
        $exception = new MinimalismException($status);

        $this->assertEquals(
            expected: '203',
            actual: $exception->getHttpCode()
        );
    }
}