<?php

namespace CarloNicora\Minimalism\Tests\Unit\Factories;

use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\DefaultServiceInterface;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ServiceFactoryTest
 * @package CarloNicora\Minimalism\Tests\Unit\Factories
 * @coversDefaultClass \CarloNicora\Minimalism\Factories\ServiceFactory
 */
class ServiceFactoryTest extends AbstractTestCase
{
    private MockObject $minimalismFactories;
    private ServiceFactory $serviceFactory;

    public function setUp(
    ): void
    {
        parent::setUp();
        $this->minimalismFactories = $this->createMock(MinimalismFactories::class);
        $this->serviceFactory = new ServiceFactory($this->minimalismFactories);
    }

    /**
     * @covers ::getPath
     * @return void
     */
    public function testItShouldGetPath(
    ): void
    {
        $path = $this->createMock(Path::class);
        $this->setProperty(
            object: $this->serviceFactory,
            parameterName: 'services',
            parameterValue: [Path::class => $path]
        );

        $result = $this->serviceFactory->getPath();

        $this->assertSame(
            expected: $path,
            actual: $result
        );
    }

    /**
     * @covers ::getDefaultService
     * @return void
     */
    public function testItShouldReturnNullForDefaultService(
    ): void
    {
        $this->assertNull($this->serviceFactory->getDefaultService());
    }
}