<?php

namespace CarloNicora\Minimalism\Tests\Unit\Factories;

use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ModelFactory;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;

/**
 * Class MinimalismFactoriesTest
 * @package CarloNicora\Minimalism\Tests\Unit\Factories
 * @coversDefaultClass \CarloNicora\Minimalism\Factories\MinimalismFactories
 */
class MinimalismFactoriesTest extends AbstractTestCase
{
    private MinimalismFactories $minimalismFactories;

    public function setUp(): void
    {
        parent::setUp();

        $this->minimalismFactories = new MinimalismFactories();
    }

    /**
     * @covers ::__construct
     * @covers ::getModelFactory
     * @return void
     */
    public function testItShouldReturnModelFactory(
    ): void
    {
        self::assertInstanceOf(
            expected: ModelFactory::class,
            actual: $this->minimalismFactories->getModelFactory()
        );
    }

    /**
     * @covers ::__construct
     * @covers ::getObjectFactory
     * @return void
     */
    public function testItShouldReturnObjectFactory(
    ): void
    {
        self::assertInstanceOf(
            expected: ObjectFactory::class,
            actual: $this->minimalismFactories->getObjectFactory()
        );
    }

    /**
     * @covers ::__construct
     * @covers ::getServiceFactory
     * @return void
     */
    public function testItShouldReturnServiceFactory(
    ): void
    {
        self::assertInstanceOf(
            expected: ServiceFactory::class,
            actual: $this->minimalismFactories->getServiceFactory()
        );
    }

    /**
     * @covers ::getNamespace
     * @return void
     */
    public function testItShouldGetNamespace(
    ): void
    {
        $this->assertEquals(
            expected: 'CarloNicora\Minimalism\Tests\Unit\Factories\MinimalismFactoriesTest',
            actual: MinimalismFactories::getNamespace(__DIR__ . '/MinimalismFactoriesTest.php')
        );
    }
}