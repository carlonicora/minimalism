<?php

namespace CarloNicora\Minimalism\Tests\Unit\Factories;

use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ModelFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;
use CarloNicora\Minimalism\Tests\Stubs\ModelStub;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

/**
 * Class ModelFactoryTest
 * @package CarloNicora\Minimalism\Tests\Unit\Factories
 * @coversDefaultClass \CarloNicora\Minimalism\Factories\ModelFactory
 */
class ModelFactoryTest extends AbstractTestCase
{
    private MockObject $minimalismFactories;
    private ModelFactory $modelFactory;
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
        $this->modelFactory = new ModelFactory($this->minimalismFactories);
    }

    /**
     * @covers ::initialiseFactory
     * @return void
     */
    public function testItShouldInitialiseFactoryWithCache(
    ): void
    {
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);
        $modelsCachePath = self::$tempDir . '/models.cache';
        $servicesModelsCachePath = self::$tempDir . '/servicesModels.cache';
        $modelsDefinitionsCachePath = self::$tempDir . '/modelsDefinitions.cache';
        file_put_contents($modelsCachePath, 'a:0:{}');
        file_put_contents($servicesModelsCachePath, 'a:0:{}');
        file_put_contents($modelsDefinitionsCachePath, 'a:0:{}');

        $this->minimalismFactories
            ->expects($this->exactly(5))
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->exactly(5))
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->exactly(4))
            ->method('getCacheFile')
            ->withConsecutive(
                ['models.cache'],
                ['models.cache'],
                ['servicesModels.cache'],
                ['modelsDefinitions.cache']
            )
            ->willReturnOnConsecutiveCalls(
                $modelsCachePath,
                $modelsCachePath,
                $servicesModelsCachePath,
                $modelsDefinitionsCachePath
            );
        $path->expects($this->once())
            ->method('setServicesModels')
            ->with([]);

        $this->modelFactory->initialiseFactory();

        $this->assertEquals(
            expected: [],
            actual: $this->getProperty(
                object: $this->modelFactory,
                parameterName: 'models'
            )
        );
        $this->assertEquals(
            expected: [],
            actual: $this->getProperty(
                object: $this->modelFactory,
                parameterName: 'modelsDefinitions'
            )
        );
    }

    /**
     * @covers ::initialiseFactory
     * @return void
     */
    public function testItShouldInitialiseFactoryWithoutCache(
    ): void
    {
        $this->modelFactory = $this->getMockBuilder(ModelFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['loadFolderModels'])
            ->getMock();
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);
        $root = 'root';
        $modelsCachePath = self::$tempDir . '/models.cache';
        $servicesModelsCachePath = self::$tempDir . '/servicesModels.cache';
        $modelsDefinitionsCachePath = self::$tempDir . '/modelsDefinitions.cache';

        $this->minimalismFactories
            ->expects($this->exactly(7))
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->exactly(7))
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->exactly(4))
            ->method('getCacheFile')
            ->withConsecutive(
                ['models.cache'],
                ['models.cache'],
                ['servicesModels.cache'],
                ['modelsDefinitions.cache']
            )
            ->willReturnOnConsecutiveCalls(
                'notExistedFile',
                $modelsCachePath,
                $servicesModelsCachePath,
                $modelsDefinitionsCachePath
            );
        $path->expects($this->once())
            ->method('getRoot')
            ->willReturn($root);
        $this->modelFactory
            ->expects($this->exactly(2))
            ->method('loadFolderModels')
            ->withConsecutive(
                [$root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Models'],
                ['additionalDir']
            )
            ->willReturn([]);
        $path->expects($this->once())
            ->method('getServicesModelsDirectories')
            ->willReturn(['additionalDir']);
        $path->expects($this->once())
            ->method('setServicesModels')
            ->with([[]]);

        $this->modelFactory->initialiseFactory();

        $this->assertEquals(
            expected: 'a:0:{}',
            actual: file_get_contents($modelsCachePath)
        );
        $this->assertEquals(
            expected: 'a:1:{i:0;a:0:{}}',
            actual: file_get_contents($servicesModelsCachePath)
        );
        $this->assertEquals(
            expected: 'a:0:{}',
            actual: file_get_contents($modelsDefinitionsCachePath)
        );
    }

    /**
     * @covers ::create
     * @return void
     */
    public function testItShouldCreateModelWithNullParameters(
    ): void
    {
        $this->modelFactory = $this->getMockBuilder(ModelFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['createParameters'])
            ->getMock();
        $parameters = $this->createMock(ModelParameters::class);
        $modelName = ModelStub::class;

        $this->modelFactory
            ->expects($this->once())
            ->method('createParameters')
            ->with($modelName)
            ->willReturn($parameters);

        /** @var ModelInterface $result */
        $result = $this->modelFactory->create($modelName);

        $this->assertInstanceOf(
            expected: ModelStub::class,
            actual: $result
        );
        $this->assertSame(
            expected: $this->minimalismFactories,
            actual: $this->getProperty(
                object: $result,
                parameterName: 'minimalismFactories'
            )
        );
        $this->assertSame(
            $parameters,
            $this->getProperty(
                object: $result,
                parameterName: 'parameters'
            )
        );
        $this->assertNull(
            $this->getProperty(
                object: $result,
                parameterName: 'function'
            )
        );
    }

    /**
     * @covers ::create
     * @return void
     */
    public function testItShouldCreateModelWithoutNulParameters(
    ): void
    {
        $modelName = ModelStub::class;
        $parameters = $this->createMock(ModelParameters::class);
        $function = 'someFunction';

        /** @var ModelInterface $result */
        $result = $this->modelFactory->create($modelName, $parameters, $function);

        $this->assertInstanceOf(
            expected: ModelStub::class,
            actual: $result
        );
        $this->assertSame(
            expected: $this->minimalismFactories,
            actual: $this->getProperty(
                object: $result,
                parameterName: 'minimalismFactories'
            )
        );
        $this->assertSame(
            expected: $parameters,
            actual: $this->getProperty(
                object: $result,
                parameterName: 'parameters'
            )
        );
        $this->assertSame(
            expected: $parameters,
            actual: $this->getProperty(
                object: $result,
                parameterName: 'parameters'
            )
        );
        $this->assertEquals(
            expected: $function,
            actual: $this->getProperty(
                object: $result,
                parameterName: 'function'
            )
        );
    }

    /**
     * @covers ::createParameters
     * @return void
     */
    public function testItShouldCreateParametersForConsole(
    ): void
    {
        $modelFactory = $this->getMockBuilder(ModelFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['getCliParameters'])
            ->getMock();
        $model = ModelStub::class;
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);
        $parameters = $this->createMock(ModelParameters::class);

        $this->minimalismFactories
            ->expects($this->once())
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getUrl')
            ->willReturn(null);
        $modelFactory->expects($this->once())
            ->method('getCliParameters')
            ->willReturn($parameters);

        $result = $this->invokeMethod(
            object: $modelFactory,
            methodName: 'createParameters',
            arguments: [&$model]
        );

        $this->assertEquals(
            expected: $parameters,
            actual: $result
        );
    }

    /**
     * @covers ::createParameters
     * @return void
     */
    public function testItShouldCreateParametersForWeb(
    ): void
    {
        $modelFactory = $this->getMockBuilder(ModelFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['getWebParameters'])
            ->getMock();
        $model = ModelStub::class;
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);
        $parameters = $this->createMock(ModelParameters::class);

        $this->minimalismFactories
            ->expects($this->once())
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getUrl')
            ->willReturn('/minimalism');
        $modelFactory->expects($this->once())
            ->method('getWebParameters')
            ->willReturn($parameters);

        $result = $this->invokeMethod(
            object: $modelFactory,
            methodName: 'createParameters',
            arguments: [&$model]
        );

        $this->assertEquals(
            expected: $parameters,
            actual: $result
        );
    }

    /**
     * @covers ::createParameters
     * @return void
     */
    public function testItShouldCreateParametersWithException(
    ): void
    {
        $modelFactory = $this->getMockBuilder(ModelFactory::class)
            ->setConstructorArgs([$this->minimalismFactories])
            ->onlyMethods(['getWebParameters'])
            ->getMock();
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);
        $model = null;

        $this->minimalismFactories
            ->expects($this->once())
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getUrl')
            ->willReturn('/minimalism');
        $modelFactory->expects($this->once())
            ->method('getWebParameters')
            ->willReturn($this->createMock(ModelParameters::class));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Model not found');

        $this->invokeMethod(
            object: $modelFactory,
            methodName: 'createParameters',
            arguments: [&$model]
        );
    }

    /**
     * @covers ::loadFolderModels
     * @return void
     */
    public function testItShouldLoadNotRootFolderModels(
    ): void
    {
        $tmpDir = $this->createTmpDir();
        mkdir($tmpDir . '/Models/Nested', 0777, true);
        file_put_contents(
            $tmpDir . '/Models/Model1.php',
        '<?php' . PHP_EOL . 'namespace Models;' . PHP_EOL . 'class Model1 {}'
        );
        file_put_contents(
            $tmpDir . '/Models/Model2.php',
            '<?php' . PHP_EOL . 'namespace Models;' . PHP_EOL . 'class Model2 {}'
        );
        file_put_contents(
            $tmpDir . '/Models/Nested/Model3.php',
            '<?php' . PHP_EOL . 'namespace Models\Nested;' . PHP_EOL . 'class Model3 {}'
        );

        $modelFactory = $this->getMockBuilder(ModelFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['initialiseModelDefinition'])
            ->getMock();

        $modelFactory->expects($this->exactly(3))
            ->method('initialiseModelDefinition')
            ->withConsecutive(
                ['Models\Model1'],
                ['Models\Nested\Model3'],
                ['Models\Model2'],
            );

        $result = $this->invokeMethod(
            object: $modelFactory,
            methodName: 'loadFolderModels',
            arguments: [$tmpDir]
        );

        $this->assertEquals(
            expected: [
                'models-folder' => [
                    'model1' => 'Models\Model1',
                    'nested-folder' => [
                        'model3' => 'Models\Nested\Model3',
                    ],
                    'model2' => 'Models\Model2',
                ],
            ],
            actual: $result
        );

        $this->recurseRmdir($tmpDir);
    }


    /**
     * @covers ::loadFolderModels
     * @return void
     */
    public function testItShouldLoadRootFolderModels(
    ): void
    {
        $tmpDir = $this->createTmpDir();
        mkdir($tmpDir . '/Models/');
        file_put_contents(
            $tmpDir . '/Models/Model1.php',
            '<?php' . PHP_EOL . 'namespace Global\Models;' . PHP_EOL . 'class Model1 {}'
        );
        file_put_contents(
            $tmpDir . '/index.php',
            '<?php' . PHP_EOL . 'namespace Global;' . PHP_EOL . 'echo 1'
        );

        $modelFactory = $this->getMockBuilder(ModelFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['initialiseModelDefinition'])
            ->getMock();

        $modelFactory->expects($this->exactly(2))
            ->method('initialiseModelDefinition')
            ->withConsecutive(
                ['Global\Models\Model1'],
                ['Global\index'],
            );

        $result = $this->invokeMethod(
            object: $modelFactory,
            methodName: 'loadFolderModels',
            arguments: [$tmpDir]
        );

        $this->assertEquals(
            expected: [
                'models-folder' => [
                    'model1' => 'Global\Models\Model1',
                ],
                'index' => 'Global\index',
                '*' => 'Global\index'
            ],
            actual: $result
        );

        $this->recurseRmdir($tmpDir);
    }

    /**
     * @covers ::recursive
     * @return void
     */
    public function testItShouldTestRecursive(
    ): void
    {
        $input = [
            '01' => 'value01',
            '02' => [
                '021' => 'value021',
                '022' => [
                    '0221' => 'value0221',
                    '0222' => ['02221' => 'value02221']
                ]
            ],
            '03' => 'value03',
        ];

        $result = $this->invokeMethod(
            object: $this->modelFactory,
            methodName: 'recursive',
            arguments: ['000', $input]
        );

        $this->assertEquals(
            expected: [
                '01' => ['000' => 'value01'],
                '02' => [
                    '021' => ['000' => 'value021'],
                    '022' => [
                        '0221' => ['000' => 'value0221'],
                        '0222' => [
                            '02221' => ['000' => 'value02221']
                        ],
                    ],
                ],
                '03' => ['000' => 'value03'],
            ],
            actual: $result
        );

        $this->assertTrue(true);
    }
}