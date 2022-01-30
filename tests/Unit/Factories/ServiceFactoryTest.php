<?php

namespace CarloNicora\Minimalism\Tests\Unit\Factories;

use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\DefaultServiceInterface;
use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;
use CarloNicora\Minimalism\Tests\Stubs\DefaultServiceStub;
use CarloNicora\Minimalism\Tests\Stubs\LoggerServiceStub;
use CarloNicora\Minimalism\Tests\Stubs\ServiceStub;
use CarloNicora\Minimalism\Tests\Stubs\TransformerServiceStub;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

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
     * @covers ::__wakeup
     * @return void
     */
    public function testItShouldWakeup(
    ): void
    {
        $serializedFactory = serialize($this->serviceFactory);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('One or more services has not released ServiceFactory correctly.');
        $this->expectExceptionCode(500);

        unserialize($serializedFactory);
    }

    /**
     * @covers ::initialiseFactory
     * @return void
     */
    public function testItShouldInitialiseFactoryFromFile(
    ): void
    {
        $tmpDir = $this->createTmpDir();
        $cacheFile = $tmpDir . '/services.cache';
        file_put_contents($cacheFile, serialize([ServiceStub::class => new ServiceStub()]));

        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['setENV','getPath', 'addService'])
            ->getMock();
        $path = $this->createMock(Path::class);
        $objectFactory = $this->createMock(ObjectFactory::class);

        $serviceFactory->expects($this->once())
            ->method('addService')
            ->with(Path::class, new Path());
        $serviceFactory->expects($this->exactly(2))
            ->method('getPath')
            ->willReturn($path);
        $serviceFactory->expects($this->once())->method('setENV');
        $path->expects($this->exactly(2))
            ->method('getCacheFile')
            ->with('services.cache')
            ->willReturn($cacheFile);
        $this->minimalismFactories
            ->expects($this->once())
            ->method('getObjectFactory')
            ->willReturn($objectFactory);

        $serviceFactory->initialiseFactory();

        $this->assertInstanceOf(
            expected: ServiceStub::class,
            actual: $serviceFactory->getService(ServiceStub::class)
        );
        $this->assertSame(
            expected: $objectFactory,
            actual: $this->getProperty(
                object: $serviceFactory->getService(ServiceStub::class),
                parameterName: 'objectFactory'
            )
        );
        $this->assertFalse(
            $this->getProperty(
                object: $this->serviceFactory,
                parameterName: 'loaded'
            )
        );

        self::recurseRmdir($tmpDir);
    }

    /**
     * @covers ::__construct
     * @covers ::initialiseFactory
     * @return void
     */
    public function testItShouldInitialiseFactory(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['setENV','getPath', 'getServiceFiles', 'create', 'setLoaded'])
            ->getMock();
        $path = $this->createMock(Path::class);

        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $serviceFactory->expects($this->once())->method('setENV');
        $path->expects($this->once())
            ->method('getCacheFile')
            ->with('services.cache')
            ->willReturn('not-existed-file');
        $serviceFactory->expects($this->once())
            ->method('getServiceFiles')
            ->willReturn([__DIR__ . '/../../Stubs/ServiceStub.php']);
        $serviceFactory->expects($this->once())
            ->method('create')
            ->with('CarloNicora\Minimalism\Tests\Stubs\ServiceStub');
        $serviceFactory->expects($this->once())
            ->method('setLoaded')
            ->with(true);

        $serviceFactory->initialiseFactory();
    }

    /**
     * @covers ::isLoaded
     * @covers ::setLoaded
     * @return void
     */
    public function testItShouldSetLoaded(
    ): void
    {
        $this->assertFalse(
            $this->getProperty(
                object: $this->serviceFactory,
                parameterName: 'loaded'
            )
        );

        $this->invokeMethod(
            object: $this->serviceFactory,
            methodName: 'setLoaded',
            arguments: [true]
        );

        $this->assertTrue(
            $this->invokeMethod(
                object: $this->serviceFactory,
                methodName: 'isLoaded'
            )
        );
    }

    /**
     * @covers ::setENV
     * @covers ::getENV
     * @return void
     */
    public function testItShouldSetENV(
    ): void
    {
        $tmpDir = $this->createTmpDir();
        file_put_contents($tmpDir . '/.env', 'PARAM = 123');

        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPath'])
            ->getMock();
        $path = $this->createMock(Path::class);

        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getRoot')
            ->willReturn($tmpDir);

        $serviceFactory->setENV();

        $this->assertEquals(
            expected: ['PARAM' => 123],
            actual: $serviceFactory->getENV()
        );

        self::recurseRmdir($tmpDir);
    }

    /**
     * @covers ::setENV
     * @covers ::getENV
     * @return void
     */
    public function testItShouldSetTestingENV(
    ): void
    {
        $tmpDir = $this->createTmpDir();
        file_put_contents($tmpDir . '/.env.testing', 'TESTING_PARAM = 321');
        $_SERVER['HTTP_TEST_ENVIRONMENT'] = true;

        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPath'])
            ->getMock();
        $path = $this->createMock(Path::class);

        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getRoot')
            ->willReturn($tmpDir);

        $serviceFactory->setENV();

        $this->assertEquals(
            expected: ['TESTING_PARAM' => 321],
            actual: $serviceFactory->getENV()
        );

        $_SERVER['HTTP_TEST_ENVIRONMENT'] = null;
        self::recurseRmdir($tmpDir);
    }

    /**
     * @covers ::setENV
     * @covers ::getENV
     * @return void
     */
    public function testItShouldSetDefaultENV(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPath'])
            ->getMock();
        $path = $this->createMock(Path::class);

        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getRoot')
            ->willReturn('');

        $serviceFactory->setENV();

        $this->assertEquals(
            expected: [],
            actual: $serviceFactory->getENV()
        );
    }

    /**
     * @covers ::addService
     * @covers ::getServices
     * @return void
     */
    public function testItShouldGetServices(
    ): void
    {
        $services = [
            Path::class => $path = $this->createMock(Path::class),
            ServiceStub::class => $service = $this->createMock(ServiceStub::class),
        ];

        $this->assertEquals(
            expected: [],
            actual: $this->serviceFactory->getServices()
        );

        $this->serviceFactory->addService(Path::class, $path);
        $this->serviceFactory->addService(ServiceStub::class, $service);

        $this->assertEquals(
            expected: $services,
            actual: $this->serviceFactory->getServices()
        );
    }

    /**
     * @covers ::addService
     * @covers ::getService
     * @return void
     */
    public function testItShouldGetService(
    ): void
    {
        $path = $this->createMock(Path::class);
        $service = $this->createMock(ServiceStub::class);

        $this->serviceFactory->addService(Path::class, $path);
        $this->serviceFactory->addService(ServiceStub::class, $service);
        $this->serviceFactory->addService(TransformerInterface::class, ServiceStub::class);

        $this->assertNull($this->serviceFactory->getService(LoggerInterface::class));
        $this->assertEquals(
            expected: $path,
            actual: $this->serviceFactory->getService(Path::class)
        );
        $this->assertEquals(
            expected: $service,
            actual: $this->serviceFactory->getService(ServiceStub::class)
        );
        $this->assertEquals(
            expected: ServiceStub::class,
            actual: $this->serviceFactory->getService(TransformerInterface::class)
        );
    }

    /**
     * @covers ::getServiceFiles
     * @return void
     */
    public function testItShouldGetServiceFiles(
    ): void
    {
        $tmpDir = $this->createTmpDir();
        // ----- prepare vendor services files -----
        mkdir($tmpDir . '/vendor/carlonicora/minimalism-service-auth/src/',  0777, true);
        file_put_contents($tmpDir . '/vendor/carlonicora/minimalism-service-auth/src/Auth.php', 'class Auth {}');
        file_put_contents($tmpDir . '/vendor/carlonicora/minimalism-service-auth/src/Guard.php', 'class Guard {}');
        file_put_contents($tmpDir . '/vendor/carlonicora/minimalism-service-auth/src/index.js', 'alert(123)');
        // ----- prepare default services files -----
        mkdir($tmpDir . '/src');
        file_put_contents($tmpDir . '/src/Kernel.php', 'class Kernel {}');
        // ----- prepare internal services files -----
        mkdir($tmpDir . '/src/Services/Parser/', 0777, true);
        file_put_contents($tmpDir . '/src/Services/Parser/Parser.php', 'class Parser {}');

        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPath'])
            ->getMock();
        $path = $this->createMock(Path::class);

        $serviceFactory->expects($this->exactly(3))
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->exactly(3))
            ->method('getRoot')
            ->willReturn($tmpDir);

        $result = $this->invokeMethod(
            object: $serviceFactory,
            methodName: 'getServiceFiles'
        );

        $this->assertCount(
            expectedCount: 4,
            haystack: $result
        );
        $this->assertContains(
            needle: '/tmp/tmp/vendor/carlonicora/minimalism-service-auth/src/Auth.php',
            haystack: $result
        );
        $this->assertContains(
            needle: '/tmp/tmp/vendor/carlonicora/minimalism-service-auth/src/Guard.php',
            haystack: $result
        );
        $this->assertContains(
            needle: '/tmp/tmp/src/Services/Parser/Parser.php',
            haystack: $result
        );
        $this->assertContains(
            needle: '/tmp/tmp/src/Kernel.php',
            haystack: $result
        );

        self::recurseRmdir($tmpDir);
    }

    /**
     * @covers ::destroy
     * @return void
     */
    public function testItShouldDestroyWithoutService(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDefaultService', 'getServices', 'getLogger'])
            ->getMock();

        $serviceFactory->expects($this->once())
            ->method('getDefaultService')
            ->willReturn(null);
        $serviceFactory->expects($this->once())
            ->method('getLogger')
            ->willReturn(null);
        $serviceFactory->expects($this->once())
            ->method('getServices')
            ->willReturn([]);

        $serviceFactory->destroy();
    }

    /**
     * @covers ::destroy
     * @return void
     */
    public function testItShouldDestroy(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDefaultService', 'getService', 'getServices', 'getLogger'])
            ->getMock();
        $defaultService = $this->createMock(DefaultServiceInterface::class);
        $delayedService1 = $this->createMock(ServiceInterface::class);
        $delayedService2 = $this->createMock(ServiceInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $service = $this->createMock(ServiceStub::class);

        $serviceFactory->expects($this->exactly(2))
            ->method('getDefaultService')
            ->willReturn($defaultService);
        $defaultService->expects($this->once())
            ->method('getDelayedServices')
            ->willReturn([$delayedService1::class, $delayedService2::class]);
        $serviceFactory->expects($this->exactly(3))
            ->method('getService')
            ->withConsecutive(
                [$delayedService1::class],
                [$delayedService2::class],
                [LoggerInterface::class],
            )
            ->willReturnOnConsecutiveCalls(
                $delayedService1,
                null,
                'Logger'
            );
        $delayedService1->expects($this->once())
            ->method('destroy');
        $serviceFactory->expects($this->once())
            ->method('getLogger')
            ->willReturn($logger);
        $serviceFactory->expects($this->once())
            ->method('getServices')
            ->willReturn([
                $delayedService1::class => $delayedService1,
                $service::class => $service,
            ]);
        $service->expects($this->once())
            ->method('destroy');

        $serviceFactory->destroy();
    }

    /**
     * @covers ::__destruct
     * @return void
     */
    public function testItShouldDestruct(
    ): void
    {
        $tmpDir = $this->createTmpDir();
        $cacheFile = $tmpDir . '/services.cache';
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isLoaded', 'getPath', 'getServices'])
            ->getMock();
        $service1 = $this->createMock(ServiceInterface::class);
        $service2 = $this->createMock(ServiceStub::class);
        $path = $this->createMock(Path::class);
        $services = [
            $service1::class => $service1,
            $service2::class => $service2,
            LoggerInterface::class => null,
        ];

        $serviceFactory->expects($this->once())
            ->method('isLoaded')
            ->willReturn(true);
        $serviceFactory->expects($this->exactly(3))
            ->method('getServices')
            ->willReturn($services);
        $service1->expects($this->once())
            ->method('unsetObjectFactory');
        $service2->expects($this->once())
            ->method('unsetObjectFactory');
        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getCacheFile')
            ->with('services.cache')
            ->willReturn($cacheFile);

        $serviceFactory->__destruct();

        $this->assertEquals(
            expected: $services,
            actual: unserialize(file_get_contents($cacheFile))
        );

        $this->recurseRmdir($tmpDir);
    }

    /**
     * @covers ::create
     * @return void
     */
    public function testItShouldNotCreateNotExistedClass(
    ): void
    {
        $this->assertNull(
            $this->serviceFactory->create('NotExistedClass')
        );
    }

    /**
     * @covers ::create
     * @return void
     */
    public function testItShouldCreateIfServiceAlreadyDefined(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getServices', 'getService'])
            ->getMock();
        $serviceObject = $this->createMock(ServiceStub::class);
        $serviceName = ServiceStub::class;

        $serviceFactory->expects($this->once())
            ->method('getServices')
            ->willReturn([ServiceStub::class => $serviceObject]);
        $serviceFactory->expects($this->once())
            ->method('getService')
            ->with($serviceName)
            ->willReturn($serviceObject);

        $result = $serviceFactory->create($serviceName);

        $this->assertSame(
            expected: $serviceObject,
            actual: $result
        );
    }

    /**
     * @covers ::create
     * @return void
     */
    public function testItShouldCreateIfServiceAlreadyDefinedString(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getServices', 'getService'])
            ->getMock();
        $serviceObject = $this->createMock(ServiceStub::class);
        $serviceName = ServiceStub::class;

        $serviceFactory->expects($this->once())
            ->method('getServices')
            ->willReturn([
                ServiceStub::class => 'otherService',
                'otherService' => $serviceObject,
            ]);
        $serviceFactory->expects($this->exactly(2))
            ->method('getService')
            ->withConsecutive(
                [$serviceName],
                ['otherService']
            )
            ->willReturnOnConsecutiveCalls(
                'otherService',
                $serviceObject
            );

        $result = $serviceFactory->create($serviceName);

        $this->assertSame(
            expected: $serviceObject,
            actual: $result
        );
    }

    /**
     * @covers ::create
     * @return void
     */
    public function testItShouldNoCreateWhenInitializeNull(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getServices', 'initialise', 'getENV'])
            ->getMock();
        $serviceName = ServiceStub::class;
        $env = ['KEY' => 'VALUE'];

        $serviceFactory->expects($this->once())
            ->method('getServices')
            ->willReturn([]);
        $serviceFactory->expects($this->once())
            ->method('getENV')
            ->willReturn($env);
        $serviceFactory->expects($this->once())
            ->method('initialise')
            ->with($serviceFactory, $serviceName, $env)
            ->willReturn(null);

        $this->assertNull($serviceFactory->create($serviceName));
    }

    /**
     * @covers ::create
     * @dataProvider servicesNamesDataProvider
     * @param string $serviceName
     * @param ServiceInterface $serviceObject
     * @param string $interface
     * @return void
     */
    public function testItShouldCreateObject(
        string $serviceName,
        ServiceInterface $serviceObject,
        string $interface
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getServices', 'initialise', 'getENV', 'addService'])
            ->getMock();
        $env = ['KEY' => 'VALUE'];

        $serviceFactory->expects($this->exactly(2))
            ->method('getServices')
            ->willReturnOnConsecutiveCalls(
                [],
                [$serviceName => $serviceObject]
            );
        $serviceFactory->expects($this->once())
            ->method('getENV')
            ->willReturn($env);
        $serviceFactory->expects($this->once())
            ->method('initialise')
            ->with($serviceFactory, $serviceName, $env)
            ->willReturn($serviceObject);
        $serviceFactory->expects($this->exactly(3))
            ->method('addService')
            ->withConsecutive(
                [$serviceName, $serviceObject],
                [$serviceObject->getBaseInterface(), $serviceName],
                [$interface, $serviceName]
            );

        $result = $serviceFactory->create($serviceName);

        $this->assertSame(
            expected: $serviceObject,
            actual: $result
        );
    }

    /**
     * @covers ::create
     * @return void
     */
    public function testItShouldThrowExceptionForBaseInterface(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getServices', 'initialise', 'getENV', 'addService'])
            ->getMock();
        $env = ['KEY' => 'VALUE'];
        $serviceName = TransformerServiceStub::class;
        $serviceObject = new TransformerServiceStub();
        $serviceFactory->expects($this->exactly(2))
            ->method('getServices')
            ->willReturnOnConsecutiveCalls(
                [$serviceObject->getBaseInterface() => ''],
                [
                    $serviceObject->getBaseInterface() => '',
                    $serviceName => $serviceObject
                ]
            );
        $serviceFactory->expects($this->once())
            ->method('getENV')
            ->willReturn($env);
        $serviceFactory->expects($this->once())
            ->method('initialise')
            ->with($serviceFactory, $serviceName, $env)
            ->willReturn($serviceObject);
        $serviceFactory->expects($this->once())
            ->method('addService')
            ->with($serviceName, $serviceObject);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('A base interface can only be extend by one service');
        $this->expectExceptionCode(500);

        $serviceFactory->create($serviceName);
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

    /**
     * @covers ::getDefaultService
     * @return void
     */
    public function testItShouldGetDefaultService(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create', 'getServices'])
            ->getMock();
        $defaultService = $this->createMock(DefaultServiceInterface::class);

        $serviceFactory->expects($this->once())
            ->method('getServices')
            ->willReturn([DefaultServiceInterface::class => '']);
        $serviceFactory->expects($this->once())
            ->method('create')
            ->with(DefaultServiceInterface::class)
            ->willReturn($defaultService);

        $result = $serviceFactory->getDefaultService();

        $this->assertSame(
            expected: $defaultService,
            actual: $result
        );
    }

    /**
     * @covers ::getTranformerService
     * @return void
     */
    public function testItShouldReturnNullForTransformerService(
    ): void
    {
        $this->assertNull($this->serviceFactory->getTranformerService());
    }

    /**
     * @covers ::getTranformerService
     * @return void
     */
    public function testItShouldGetTransformerService(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create', 'getServices'])
            ->getMock();
        $transformer = $this->createMock(TransformerInterface::class);

        $serviceFactory->expects($this->once())
            ->method('getServices')
            ->willReturn([TransformerInterface::class => '']);
        $serviceFactory->expects($this->once())
            ->method('create')
            ->with(TransformerInterface::class)
            ->willReturn($transformer);

        $result = $serviceFactory->getTranformerService();

        $this->assertSame(
            expected: $transformer,
            actual: $result
        );
    }

    /**
     * @covers ::getLogger
     * @return void
     */
    public function testItShouldReturnNullForLogger(
    ): void
    {
        $this->assertNull($this->serviceFactory->getLogger());
    }

    /**
     * @covers ::getLogger
     * @return void
     */
    public function testItShouldGetLogger(
    ): void
    {
        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create', 'getServices'])
            ->getMock();
        $logger = $this->createMock(LoggerInterface::class);

        $serviceFactory->expects($this->once())
            ->method('getServices')
            ->willReturn([LoggerInterface::class => '']);
        $serviceFactory->expects($this->once())
            ->method('create')
            ->with(LoggerInterface::class)
            ->willReturn($logger);

        $result = $serviceFactory->getLogger();

        $this->assertSame(
            expected: $logger,
            actual: $result
        );
    }

    /**
     * @return array
     */
    public function servicesNamesDataProvider(
    ): array
    {
        return [
            [TransformerServiceStub::class, new TransformerServiceStub(), TransformerInterface::class],
            [DefaultServiceStub::class, new DefaultServiceStub(), DefaultServiceInterface::class],
            [LoggerServiceStub::class, new LoggerServiceStub(new Path()), LoggerInterface::class],
        ];
    }
}