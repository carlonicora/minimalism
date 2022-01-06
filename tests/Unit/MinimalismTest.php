<?php
namespace CarloNicora\Minimalism\Tests\Unit;

use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;
use Exception;

class MinimalismTest extends AbstractTestCase
{
    /**
     * @return void
     *
     * @test
     * @covers \CarloNicora\Minimalism\Minimalism::__construct()
     */
    public function SHOULD_NotThrowAnyError_WHEN_MinimalismIsInstantiated(
    ): void
    {
        $minimalism = $this->generateMinimalism();
        self::assertNotNull($minimalism);
    }

    /**
     * @return void
     * @throws Exception
     *
     * @test
     * @covers \CarloNicora\Minimalism\Minimalism::getService()
     *
     */
    public function SHOULD_ReturnPath_WHEN_GetServiceIsCalledUsingPathClass(
    ): void
    {
        self::assertEquals(
            expected: $this->generateMinimalism()->getMinimalismFactories()->getServiceFactory()->getPath(),
            actual: $this->generateMinimalism()->getService(Path::class),
        );
    }

    /**
     * @return void
     * @throws Exception
     *
     * @test
     * @covers \CarloNicora\Minimalism\Minimalism::getMinimalismFactories()
     */
    public function SHOULD_ReturnValidMinimalismFactories_WHEN_IAskItFromMinimalism(
    ): void
    {
        self::assertTrue(
            condition: ($this->generateMinimalism()->getMinimalismFactories() instanceof MinimalismFactories),
        );
    }
}