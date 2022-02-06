<?php

namespace CarloNicora\Minimalism\Tests\Unit\Abstracts;

use CarloNicora\Minimalism\Abstracts\AbstractService;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;

/**
 * Class AbstractServiceTest
 * @package CarloNicora\Minimalism\Tests\Unit\Abstracts
 * @coversDefaultClass \CarloNicora\Minimalism\Abstracts\AbstractService
 */
class AbstractServiceTest extends AbstractTestCase
{
    private AbstractService $abstractService;

    public function setUp(): void
    {
        parent::setUp();
        $this->abstractService = new class () extends AbstractService {};
    }

    /**
     * @covers ::initialise
     * @return void
     */
    public function testItShouldInitialise(
    ): void
    {
        $this->abstractService->initialise();

        $this->assertTrue(true);
    }

    /**
     * @covers ::postIntialise
     * @return void
     */
    public function testItShouldPostInitialise(
    ): void
    {
        $this->abstractService->postIntialise($this->createMock(ServiceFactory::class));

        $this->assertTrue(true);
    }

    /**
     * @covers ::destroy
     * @return void
     */
    public function testItShouldDestroy(
    ): void
    {
        $this->abstractService->destroy();

        $this->assertTrue(true);
    }

    /**
     * @covers ::setObjectFactory
     * @return void
     */
    public function testItShouldSetObjectFactory(
    ): void
    {
        $objectFactory = $this->createMock(ObjectFactory::class);

        $this->abstractService->setObjectFactory($objectFactory);

        $this->assertSame(
            expected: $objectFactory,
            actual: $this->getProperty(
                object: $this->abstractService,
                parameterName: 'objectFactory'
            )
        );
    }

    /**
     * @covers ::unsetObjectFactory
     * @return void
     */
    public function testItShouldUsetObjectFactory(
    ): void
    {
        $this->abstractService->setObjectFactory($this->createMock(ObjectFactory::class));

        $this->abstractService->unsetObjectFactory();

        $this->assertNull(
            $this->getProperty(
                object: $this->abstractService,
                parameterName: 'objectFactory'
            )
        );
    }

    /**
     * @covers ::getBaseInterface
     * @return void
     */
    public function testItShouldGetBaseInterface(
    ): void
    {
        $this->assertNull($this->abstractService->getBaseInterface());
    }
}