<?php

namespace CarloNicora\Minimalism\Tests\Unit\Abstracts;

use CarloNicora\Minimalism\Abstracts\AbstractService;
use CarloNicora\Minimalism\Factories\ObjectFactory;
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
     * @covers ::destroy
     * @return void
     */
    public function testItShouldDestroy(
    ): void
    {
        $this->abstractService->setObjectFactory($this->createMock(ObjectFactory::class));

        $this->abstractService->destroy();

        $this->assertNull(
            $this->getProperty(
                object: $this->abstractService,
                parameterName: 'objectFactory'
            )
        );
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
}