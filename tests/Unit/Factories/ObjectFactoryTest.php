<?php

namespace CarloNicora\Minimalism\Tests\Unit\Factories;

use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;
use CarloNicora\Minimalism\Tests\Stubs\ComplexObjectFactoryStub;
use CarloNicora\Minimalism\Tests\Stubs\ComplexObjectStub;
use CarloNicora\Minimalism\Tests\Stubs\SimpleObject1Stub;
use CarloNicora\Minimalism\Tests\Stubs\SimpleObject2Stub;
use RuntimeException;
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
     * @covers ::setObjectsFactoriesDefinitionsCache
     * @covers ::setObjectsDefinitionsCache
     * @covers ::setObjectsFactoriesDefinitions
     * @covers ::setObjectsDefinitions
     * @return void
     */
    public function testItShouldInitializeFactory(
    ): void
    {
        $objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['clearPool'])
            ->getMock();
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);
        $objectsFactoriesDefinitionsCachePath = self::$tempDir . '/objectsFactoriesDefinitions.cache';
        $objectsDefinitionsCache = self::$tempDir . '/objectsDefinitions.cache';
        file_put_contents($objectsFactoriesDefinitionsCachePath, serialize([new \stdClass()]));
        file_put_contents($objectsDefinitionsCache, serialize([new \stdClass(), new \stdClass()]));

        $objectFactory->expects($this->once())->method('clearPool');
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

        $objectFactory->initialiseFactory();
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
     * @covers ::__wakeup
     * @return void
     */
    public function testItShouldWakeup(
    ): void
    {
        $serializedFactory = serialize($this->objectFactory);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('One or more services has not released ObjectFactory correctly.');
        $this->expectExceptionCode(500);

        unserialize($serializedFactory);
    }

    /**
     * @covers ::destroy
     * @return void
     */
    public function testItShouldDestroyWhenCacheEmpty(
    ): void
    {
        $objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['clearPool'])
            ->getMock();
        $objectFactory->setObjectUpdated(true);
        $objectFactory->setObjectsDefinitionsCache(null);
        $objectFactory->setObjectsFactoriesDefinitionsCache(null);

        $objectFactory->expects($this->once())->method('clearPool');

        $objectFactory->destroy();
    }

    /**
     * @covers ::destroy
     * @return void
     */
    public function testItShouldDestroy(
    ): void
    {
        $objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['clearPool'])
            ->getMock();
        $objectsFactoriesDefinitionsCacheFile = self::$tempDir . '/objectsFactoriesDefinitionsCache';
        $objectsDefinitionsCacheFile = self::$tempDir . '/objectsDefinitionsCache';
        $objectsFactoriesDefinitions = ['objectsFactoriesDefinitions'];
        $objectsDefinitions = ['objectsDefinitions'];
        $objectFactory->setObjectUpdated(true);
        $objectFactory->setObjectsDefinitionsCache(null);
        $objectFactory->setObjectsFactoriesDefinitionsCache(null);
        $objectFactory->setObjectsFactoriesDefinitionsCache($objectsFactoriesDefinitionsCacheFile);
        $objectFactory->setObjectsFactoriesDefinitions($objectsFactoriesDefinitions);
        $objectFactory->setObjectsDefinitionsCache($objectsDefinitionsCacheFile);
        $objectFactory->setObjectsDefinitions($objectsDefinitions);

        $objectFactory->expects($this->once())->method('clearPool');

        $objectFactory->destroy();

        $this->assertEquals(
            expected: serialize($objectsFactoriesDefinitions),
            actual: file_get_contents($objectsFactoriesDefinitionsCacheFile)
        );
        $this->assertEquals(
            expected: serialize($objectsDefinitions),
            actual: file_get_contents($objectsDefinitionsCacheFile)
        );
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
        $className = SimpleObject1Stub::class;
        $parameters = $this->createMock(ModelParameters::class);
        $this->objectFactory->setObjectsDefinitions([$className => []]);
        $this->objectFactory->setObjectsFactoriesDefinitions([]);
        $expectedSimpleObject = new SimpleObject1Stub();

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
        $className = SimpleObject1Stub::class;
        $parameters = $this->createMock(ModelParameters::class);
        $expectedObject = new SimpleObject1Stub();

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
     * @covers ::createComplexObject
     * @return void
     */
    public function testItShouldCreateComplexObjectIfFactoryDefined(
    ): void
    {
        $objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generateMethodParametersValues'])
            ->getMock();
        $name = 'complexName';
        $parameters = $this->createMock(ModelParameters::class);
        $className = ComplexObjectStub::class;
        $objectFactory->setObjectsFactoriesDefinitions([
            $className => [
                'factoryName' => ComplexObjectFactoryStub::class,
                'coonstructMethodParameters' => []
            ],
        ]);

        $objectFactory->expects($this->once())
            ->method('generateMethodParametersValues')
            ->with([], $parameters)
            ->willReturn([]);

        $result = $this->invokeMethod(
            object: $objectFactory,
            methodName: 'createComplexObject',
            arguments: [$className, $name, $parameters]
        );

        $this->assertInstanceOf(
            expected: ComplexObjectStub::class,
            actual: $result
        );
    }

    /**
     * @covers ::createComplexObject
     * @return void
     */
    public function testItShouldThrowExceptionWhileCreatingObject(
    ): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Missing factory name');

        $this->invokeMethod(
            object: $this->objectFactory,
            methodName: 'createComplexObject',
            arguments: ['NotExistedClass', null, null]
        );
    }

    /**
     * @covers ::createComplexObject
     * @return void
     */
    public function testItShouldCreateComplexObjectIfFactoryNotDefined(
    ): void
    {
        $objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generateMethodParametersValues', 'getMethodParametersDefinition', 'setObjectUpdated'])
            ->getMock();
        $name = 'complexName';
        $parameters = $this->createMock(ModelParameters::class);
        $className = ComplexObjectStub::class;

        $objectFactory->expects($this->once())
            ->method('getMethodParametersDefinition')
            ->willReturn([]);
        $objectFactory->expects($this->once())
            ->method('generateMethodParametersValues')
            ->with([], $parameters)
            ->willReturn([]);
        $objectFactory->expects($this->once())
            ->method('setObjectUpdated')
            ->with(true);

        $result = $this->invokeMethod(
            object: $objectFactory,
            methodName: 'createComplexObject',
            arguments: [$className, $name, $parameters]
        );

        $this->assertInstanceOf(
            expected: ComplexObjectStub::class,
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
            ->onlyMethods(['getMethodParametersDefinition', 'setObjectUpdated', 'generateMethodParametersValues'])
            ->getMock();
        $className = SimpleObject1Stub::class;
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
        $this->objectFactory
            ->expects($this->once())
            ->method('setObjectUpdated')
            ->with(true);

        $result = $this->objectFactory->createSimpleObject($className, $parameters);

        $this->assertInstanceOf(SimpleObject1Stub::class, $result);
        $this->assertEquals(
            expected: 'someParam',
            actual: $this->getProperty(object: $result, parameterName: 'name')
        );
    }

    /**
     * @covers ::createSimpleObject
     * @return void
     */
    public function testItShouldCreateSimpleObjectWithoutConstructor(
    ): void
    {
        $this->objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['getMethodParametersDefinition', 'setObjectUpdated', 'generateMethodParametersValues'])
            ->getMock();
        $className = SimpleObject2Stub::class;
        $parameters = $this->createMock(ModelParameters::class);
        $this->objectFactory
            ->expects($this->once())
            ->method('generateMethodParametersValues')
            ->with([], $parameters)
            ->willReturn([]);
        $this->objectFactory
            ->expects($this->once())
            ->method('setObjectUpdated')
            ->with(true);

        $result = $this->objectFactory->createSimpleObject($className, $parameters);

        $this->assertInstanceOf(SimpleObject2Stub::class, $result);
    }

    /**
     * @covers ::createSimpleObject
     * @return void
     */
    public function testItShouldCreateSimpleObjectWithDefinedParametersDefinition(
    ): void
    {
        $this->objectFactory = $this->getMockBuilder(ObjectFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['generateMethodParametersValues'])
            ->getMock();
        $this->objectFactory->setObjectsDefinitions([
            SimpleObject1Stub::class => []
        ]);
        $className = SimpleObject1Stub::class;
        $parameters = $this->createMock(ModelParameters::class);
        $this->objectFactory
            ->expects($this->once())
            ->method('generateMethodParametersValues')
            ->with([], $parameters)
            ->willReturn(['argName']);

        $result = $this->objectFactory->createSimpleObject($className, $parameters);

        $this->assertInstanceOf(SimpleObject1Stub::class, $result);
        $this->assertEquals(
            expected: 'argName',
            actual: $this->getProperty(object: $result, parameterName: 'name')
        );
    }

    /**
     * @covers ::setObjectUpdated
     * @return void
     */
    public function testItShouldSetObjectUpdated(
    ): void
    {
        $this->assertFalse(
            $this->getProperty(
                object: $this->objectFactory,
                parameterName: 'objectUpdated'
            )
        );

        $this->objectFactory->setObjectUpdated(true);

        $this->assertTrue(
            $this->getProperty(
                object: $this->objectFactory,
                parameterName: 'objectUpdated'
            )
        );
    }

    /**
     * @covers ::clearPool
     * @return void
     */
    public function testItShouldClearPool(
    ): void
    {
        $this->setProperty(
            object: $this->objectFactory,
            parameterName: 'pool',
            parameterValue: [1,2,3]
        );

        $this->invokeMethod(
            object: $this->objectFactory,
            methodName: 'clearPool'
        );

        $this->assertEquals(
            expected: [],
            actual: $this->getProperty(
                object: $this->objectFactory,
                parameterName: 'pool'
            )
        );
    }
}