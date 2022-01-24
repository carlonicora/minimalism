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
use CarloNicora\Minimalism\Tests\Stubs\ServiceStub;
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
     * @covers ::initialiseFactory
     * @return void
     */
    public function testItShouldInitialiseFactoryFromFile(
    ): void
    {
        if (!file_exists(sys_get_temp_dir())){
            mkdir(sys_get_temp_dir());
        }
        $tmpDir = sys_get_temp_dir().'/tmp';
        if (!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }
        $cacheFile = $tmpDir . '/services.cache';
        file_put_contents($cacheFile, serialize([ServiceStub::class => new ServiceStub()]));

        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['setENV','getPath'])
            ->getMock();
        $path = $this->createMock(Path::class);
        $objectFactory = $this->createMock(ObjectFactory::class);

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

        $services = $this->getProperty(
            object: $serviceFactory,
            parameterName: 'services'
        );

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
            $this->getProperty(
                object: $this->serviceFactory,
                parameterName: 'loaded'
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
        if (!file_exists(sys_get_temp_dir())){
            mkdir(sys_get_temp_dir());
        }
        $tmpDir = sys_get_temp_dir().'/tmp';
        if (!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }
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
        if (!file_exists(sys_get_temp_dir())){
            mkdir(sys_get_temp_dir());
        }
        $tmpDir = sys_get_temp_dir().'/tmp';
        if (!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }
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
     * @covers ::getServices
     * @return void
     */
    public function testItShouldGetServices(
    ): void
    {
        $services = [
            Path::class => $this->createMock(Path::class),
            ServiceStub::class => $this->createMock(ServiceStub::class),
        ];

        $this->assertEquals(
            expected: [],
            actual: $this->serviceFactory->getServices()
        );

        $this->setProperty(
            object: $this->serviceFactory,
            parameterName: 'services',
            parameterValue: $services
        );

        $this->assertEquals(
            expected: $services,
            actual: $this->serviceFactory->getServices()
        );
    }

    /**
     * @covers ::getService
     * @return void
     */
    public function testItShouldGetService(
    ): void
    {
        $services = [
            Path::class => $path = $this->createMock(Path::class),
            ServiceStub::class => $service = $this->createMock(ServiceStub::class),
        ];
        $this->setProperty(
            object: $this->serviceFactory,
            parameterName: 'services',
            parameterValue: $services
        );

        $this->assertNull($this->serviceFactory->getService(LoggerInterface::class));
        $this->assertEquals(
            expected: $path,
            actual: $this->serviceFactory->getService(Path::class)
        );
        $this->assertEquals(
            expected: $service,
            actual: $this->serviceFactory->getService(ServiceStub::class)
        );
    }

    /**
     * @covers ::getServiceFiles
     * @return void
     */
    public function testItShouldGetServiceFiles(
    ): void
    {
        // ------- prepare temp directory -------
        if (!file_exists(sys_get_temp_dir())){
            mkdir(sys_get_temp_dir());
        }

        $tmpDir = sys_get_temp_dir().'/tmp';

        if (!file_exists($tmpDir)) {
            mkdir($tmpDir);
        }
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
        // ----------------------------------------

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
}