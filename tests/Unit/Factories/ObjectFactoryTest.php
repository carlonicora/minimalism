<?php

namespace CarloNicora\Minimalism\Tests\Unit\Factories;

use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;
use CarloNicora\Minimalism\Tests\Stubs\ComplexObjectStub;
use CarloNicora\Minimalism\Tests\Stubs\SimpleObjectStub;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class ObjectFactoryTest
 * @package CarloNicora\Minimalism\Tests\Unit\Factories
 * @coversDefaultClass \CarloNicora\Minimalism\Factories\ObjectFactory
 */
class ObjectFactoryTest extends AbstractTestCase
{
    private MockObject $minimalismFactories;
    private ?ObjectFactory $objectFactory;
    private static string $tempDir;

    /**
     * @before
     */
    public static function setUpTempDir(
    ): void
    {
        if (!file_exists(sys_get_temp_dir())){
            mkdir(sys_get_temp_dir());
        }
        self::$tempDir = sys_get_temp_dir().'/tmp';
        if (!file_exists(self::$tempDir)) {
            mkdir(self::$tempDir);
        }
    }

    /**
     * @afterClass
     */
    public function tearDownTempDir(
    ): void
    {
        self::recurseRmdir(self::$tempDir);
        self::$tempDir = null;
    }

    protected function tearDown(
    ): void
    {
        self::recurseRmdir(self::$tempDir);
    }

    public function setUp(
    ): void
    {
        parent::setUp();
        $this->minimalismFactories = $this->createMock(MinimalismFactories::class);
        $this->objectFactory = new ObjectFactory($this->minimalismFactories);
    }

    /**
     * @covers ::initialiseFactory
     * @return void
     */
    public function testItShouldInitializeFactory(
    ): void
    {
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);
        $objectsFactoriesDefinitionsCachePath = self::$tempDir . '/objectsFactoriesDefinitions.cache';
        $objectsDefinitionsCache = self::$tempDir . '/objectsDefinitions.cache';
        file_put_contents($objectsFactoriesDefinitionsCachePath, 'a:1:{i:0;O:8:"stdClass":0:{}}');
        file_put_contents($objectsDefinitionsCache, 'a:1:{i:0;O:8:"stdClass":0:{}}');

        $this->minimalismFactories
            ->expects($this->exactly(4))
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->exactly(4))
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->exactly(4))
            ->method('getCacheFile')
            ->withConsecutive(
                ['objectsFactoriesDefinitions.cache'],
                ['objectsFactoriesDefinitions.cache'],
                ['objectsDefinitions.cache'],
                ['objectsDefinitions.cache']
            )
            ->willReturnOnConsecutiveCalls(
                $objectsFactoriesDefinitionsCachePath,
                $objectsFactoriesDefinitionsCachePath,
                $objectsDefinitionsCache,
                $objectsDefinitionsCache
            );

        $this->objectFactory->initialiseFactory();

        $this->assertEquals(
            expected: [new \stdClass()],
            actual: $this->getProperty(
                object: $this->objectFactory,
                parameterName: 'objectsFactoriesDefinitions'
            )
        );
        $this->assertEquals(
            expected: [new \stdClass()],
            actual: $this->getProperty(
                object: $this->objectFactory,
                parameterName: 'objectsDefinitions'
            )
        );
    }

    /**
     * @covers ::initialiseFactory
     * @return void
     */
    public function testItShouldNotInitializeFactory(
    ): void
    {
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);

        $this->minimalismFactories
            ->expects($this->exactly(2))
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->exactly(2))
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->exactly(2))
            ->method('getCacheFile')
            ->withConsecutive(
                ['objectsFactoriesDefinitions.cache'],
                ['objectsDefinitions.cache']
            )
            ->willReturn('notExistedPath');

        $this->objectFactory->initialiseFactory();

        $this->assertEquals(
            expected: [],
            actual: $this->getProperty(
                object: $this->objectFactory,
                parameterName: 'objectsFactoriesDefinitions'
            )
        );
        $this->assertEquals(
            expected: [],
            actual: $this->getProperty(
                object: $this->objectFactory,
                parameterName: 'objectsDefinitions'
            )
        );
    }

    /**
     * @covers ::__destruct
     * @return void
     */
    public function testItShouldDestruct(
    ): void
    {
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);
        $objectsFactoriesDefinitionsCachePath = self::$tempDir . '/objectsFactoriesDefinitions.cache';
        $objectsDefinitionsCache = self::$tempDir . '/objectsDefinitions.cache';
        $this->setProperty(
            object: $this->objectFactory,
            parameterName: 'objectUpdated',
            parameterValue: true
        );
        $this->setProperty(
            object: $this->objectFactory,
            parameterName: 'objectsFactoriesDefinitions',
            parameterValue: []
        );
        $this->setProperty(
            object: $this->objectFactory,
            parameterName: 'objectsDefinitions',
            parameterValue: []
        );

        $this->minimalismFactories
            ->expects($this->exactly(2))
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->exactly(2))
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->exactly(2))
            ->method('getCacheFile')
            ->withConsecutive(
                ['objectsFactoriesDefinitions.cache'],
                ['objectsDefinitions.cache']
            )
        ->willReturnOnConsecutiveCalls(
            $objectsFactoriesDefinitionsCachePath,
            $objectsDefinitionsCache
        );

        $this->objectFactory = null;

        $this->assertEquals(
            expected: 'a:0:{}',
            actual: file_get_contents($objectsFactoriesDefinitionsCachePath)
        );
        $this->assertEquals(
            expected: 'a:0:{}',
            actual: file_get_contents($objectsDefinitionsCache)
        );
    }

    /**
     * @covers ::__destruct
     * @return void
     */
    public function testItShouldNotDestruct(
    ): void
    {
        $objectsFactoriesDefinitionsCachePath = self::$tempDir . '/objectsFactoriesDefinitions.cache';
        $objectsDefinitionsCache = self::$tempDir . '/objectsDefinitions.cache';
        $this->setProperty(
            object: $this->objectFactory,
            parameterName: 'objectUpdated',
            parameterValue: false
        );

        $this->objectFactory = null;

        $this->assertFileDoesNotExist($objectsFactoriesDefinitionsCachePath);
        $this->assertFileDoesNotExist($objectsDefinitionsCache);
    }

    /**
     * @covers ::create
     * @return void
     */
    public function testItShouldCreateSimpleObjectWhichInObjectsDefinitions(
    ): void
    {
        $this->objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['createSimpleObject'])
            ->getMock();
        $className = SimpleObjectStub::class;
        $parameters = $this->createMock(ModelParameters::class);
        $this->setProperty(
            object: $this->objectFactory,
            parameterName: 'objectsDefinitions',
            parameterValue: [$className => '']
        );
        $this->setProperty(
            object: $this->objectFactory,
            parameterName: 'objectsFactoriesDefinitions',
            parameterValue: []
        );
        $expectedSimpleObject = new SimpleObjectStub();

        $this->objectFactory
            ->expects($this->once())
            ->method('createSimpleObject')
            ->with($className, $parameters)
            ->willReturn($expectedSimpleObject);

        $result = $this->objectFactory->create($className, null, $parameters);

        $this->assertSame(
            expected: $expectedSimpleObject,
            actual: $result
        );
    }

    /**
     * @covers ::create
     * @return void
     */
    public function testItShouldCreateSimpleObjectWithInterface(
    ): void
    {
        $this->objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['createSimpleObject'])
            ->getMock();
        $className = SimpleObjectStub::class;
        $parameters = $this->createMock(ModelParameters::class);
        $expectedObject = new SimpleObjectStub();

        $this->objectFactory
            ->expects($this->once())
            ->method('createSimpleObject')
            ->with($className, $parameters)
            ->willReturn($expectedObject);

        $result = $this->objectFactory->create($className, null, $parameters);

        $this->assertSame(
            expected: $expectedObject,
            actual: $result
        );
    }

    /**
     * @covers ::create
     * @return void
     */
    public function testItShouldCreateComplexObject(
    ): void
    {
        $this->objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['createComplexObject'])
            ->getMock();

        $className = ComplexObjectStub::class;
        $name = 'someName';
        $parameters = $this->createMock(ModelParameters::class);
        $expectedObject = new ComplexObjectStub();

        $this->objectFactory
            ->expects($this->once())
            ->method('createComplexObject')
            ->with($className, $name, $parameters)
            ->willReturn($expectedObject);

        $result = $this->objectFactory->create($className, $name, $parameters);

        $this->assertSame(
            expected: $expectedObject,
            actual: $result
        );
    }

    /**
     * @covers ::createSimpleObject
     * @return void
     */
    public function testItShouldCreateSimpleObject(
    ): void
    {
        $this->objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['getMethodParametersDefinition', 'generateMethodParametersValues'])
            ->getMock();
        $className = SimpleObjectStub::class;
        $parameters = $this->createMock(ModelParameters::class);
        $this->objectFactory
            ->expects($this->once())
            ->method('getMethodParametersDefinition')
            ->with((new \ReflectionClass($className))->getMethod('__construct'))
            ->willReturn([]);
        $this->objectFactory
            ->expects($this->once())
            ->method('generateMethodParametersValues')
            ->with([], $parameters)
            ->willReturn(['someParam']);

        $result = $this->objectFactory->createSimpleObject($className, $parameters);

        $this->assertInstanceOf(SimpleObjectStub::class, $result);
        $this->assertEquals(
            expected: 'someParam',
            actual: $this->getProperty(object: $result, parameterName: 'name')
        );
    }
}