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
        self::$tempDir = sys_get_temp_dir().'/tmp';
        mkdir(self::$tempDir);

        echo 'HERE'.self::$tempDir;
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
}