<?php
namespace CarloNicora\Minimalism\Tests\Unit\Enums;

use CarloNicora\Minimalism\Enums\LogLevel;
use PHPUnit\Framework\TestCase;

class LogLevelTest extends TestCase
{
    /**
     * @return void
     */
    public function testDebug(
    ): void
    {
        self::assertEquals(
            expected: 100,
            actual: LogLevel::Debug->value,
        );
    }

    /**
     * @return void
     */
    public function testInfo(
    ): void
    {
        self::assertEquals(
            expected: 200,
            actual: LogLevel::Info->value,
        );
    }

    /**
     * @return void
     */
    public function testNotice(
    ): void
    {
        self::assertEquals(
            expected: 250,
            actual: LogLevel::Notice->value,
        );
    }

    /**
     * @return void
     */
    public function testWarning(
    ): void
    {
        self::assertEquals(
            expected: 300,
            actual: LogLevel::Warning->value,
        );
    }

    /**
     * @return void
     */
    public function testError(
    ): void
    {
        self::assertEquals(
            expected: 400,
            actual: LogLevel::Error->value,
        );
    }

    /**
     * @return void
     */
    public function testCritical(
    ): void
    {
        self::assertEquals(
            expected: 500,
            actual: LogLevel::Critical->value,
        );
    }

    /**
     * @return void
     */
    public function testAlert(
    ): void
    {
        self::assertEquals(
            expected: 550,
            actual: LogLevel::Alert->value,
        );
    }

    /**
     * @return void
     */
    public function testEmergency(
    ): void
    {
        self::assertEquals(
            expected: 600,
            actual: LogLevel::Emergency->value,
        );
    }
}