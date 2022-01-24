<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services;

use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;

/**
 * Class PathTest
 * @package CarloNicora\Minimalism\Tests\Unit\Services
 * @coversDefaultClass \CarloNicora\Minimalism\Services\Path
 */
class PathTest extends AbstractTestCase
{
    private Path $path;

    public function setUp(): void
    {
        parent::setUp();

        $this->path = new Path();
    }

    /**
     * @covers ::initialise
     * @return void
     */
    public function testItShouldTestInitializeInCLIMode(
    ): void
    {
        $this->path = $this->getMockBuilder(Path::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isCLIMode'])
            ->getMock();

        $this->path
            ->expects($this->once())
            ->method('isCLIMode')
            ->willReturn(true);

        $this->path->initialise();

        $this->assertNull($this->getProperty($this->path, 'url'));
    }

    /**
     * @covers ::initialise
     * @return void
     */
    public function testItShouldTestInitialize(
    ): void
    {
        $this->path = $this->getMockBuilder(Path::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isCLIMode', 'getProtocol', 'sanitiseUriVersion'])
            ->getMock();
        $_SERVER['REQUEST_URI'] = '/v1/mvc-framework';
        $_SERVER['HTTP_HOST'] = 'minimalism';
        $this->path
            ->expects($this->once())
            ->method('isCLIMode')
            ->willReturn(false);
        $this->path
            ->expects($this->once())
            ->method('getProtocol')
            ->willReturn('https');
        $this->path
            ->expects($this->once())
            ->method('sanitiseUriVersion')
            ->with('/v1/mvc-framework')
            ->willReturn('v1');

        $this->path->initialise();

        $this->assertEquals(
            expected: 'https://minimalism/v1/',
            actual: $this->getProperty($this->path, 'url')
        );

        unset( $_SERVER['HTTP_HOST']);
        unset($_SERVER['REQUEST_URI']);
    }

    /**
     * @covers ::sanitiseUriVersion
     * @return void
     */
    public function testItShouldSanitiseUriVersion(
    ): void
    {
        $uri = 'zxcv/v2/qwerty/uuid';

        $result = $this->path->sanitiseUriVersion($uri);

        $this->assertEquals(
            expected: 'v2',
            actual: $result
        );
        $this->assertEquals(
            expected: '/qwerty/uuid',
            actual: $uri
        );
    }

    /**
     * @covers ::getRoot
     * @return void
     */
    public function testItShouldGetRoot(
    ): void
    {
        $this->setProperty(
            object: $this->path,
            parameterName: 'root',
            parameterValue: 'rootPath'
        );

        $this->assertEquals(
            expected: 'rootPath',
            actual: $this->path->getRoot()
        );
    }

    /**
     * @covers ::getUrl
     * @return void
     */
    public function testItShouldGetUrl(
    ): void
    {
        $this->setProperty(
            object: $this->path,
            parameterName: 'url',
            parameterValue: 'https://minimalism'
        );

        $this->assertEquals(
            expected: 'https://minimalism',
            actual: $this->path->getUrl()
        );
    }

    /**
     * @covers ::getUri
     * @return void
     */
    public function testItShouldGetUri(
    ): void
    {
        $this->setProperty(
            object: $this->path,
            parameterName: 'uri',
            parameterValue: 'qwerty'
        );

        $this->assertEquals(
            expected: 'qwerty',
            actual: $this->path->getUri()
        );
    }

    /**
     * @covers ::getCacheFile
     * @return void
     */
    public function testItShouldGetCacheFile(
    ): void
    {
        $this->path = $this->getMockBuilder(Path::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRoot'])
            ->getMock();

        $this->path
            ->expects($this->once())
            ->method('getRoot')
            ->willReturn('root-path');

        $result = $this->path->getCacheFile('super-service');

        $this->assertEquals(
            expected: 'root-path/cache/super-service',
            actual: $result
        );
    }

    /**
     * @covers ::setServicesModels
     * @covers ::getServicesModels
     * @return void
     */
    public function testItShouldTestServicesModels(
    ): void
    {
        $serviceModels = ['model1', 'model2'];

        $this->assertEquals(
            expected: [],
            actual: $this->path->getServicesModels()
        );

        $this->path->setServicesModels($serviceModels);

        $this->assertEquals(
            expected: $serviceModels,
            actual: $this->path->getServicesModels()
        );
    }

    /**
     * @covers ::getServicesModelsDirectories
     * @covers ::addServiceModelDirectory
     * @return void
     */
    public function testItShouldTestServicesModelsDirectories(
    ): void
    {
        $directory = 'minimalism/models-directory';

        $this->assertEquals(
            expected: [],
            actual: $this->path->getServicesModelsDirectories()
        );

        $this->path->addServiceModelDirectory($directory);

        $this->assertEquals(
            expected: [$directory],
            actual: $this->path->getServicesModelsDirectories()
        );
    }

    /**
     * @covers ::getServicesViewsDirectories
     * @covers ::addServiceViewDirectory
     * @return void
     */
    public function testItShouldTestServicesViewsDirectories(
    ): void
    {
        $directory = 'minimalism/views-directory';

        $this->assertEquals(
            expected: [],
            actual: $this->path->getServicesViewsDirectories()
        );

        $this->path->addServiceViewDirectory($directory);

        $this->assertEquals(
            expected: [$directory],
            actual: $this->path->getServicesViewsDirectories()
        );
    }

    /**
     * @covers ::getProtocol
     * @return void
     */
    public function testItShouldGetHttpProtocol(
    ): void
    {
        $this->assertEquals(
            expected: 'http',
            actual: $this->path->getProtocol()
        );
    }

    /**
     * @covers ::getProtocol
     * @return void
     */
    public function testItShouldGetHttpsProtocolWithXForwardedProto(
    ): void
    {
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

        $this->assertEquals(
            expected: 'https',
            actual: $this->path->getProtocol()
        );

        unset($_SERVER['HTTP_X_FORWARDED_PROTO']);
    }

    /**
     * @covers ::getProtocol
     * @return void
     */
    public function testItShouldGetHttpsProtocol(
    ): void
    {
        $_SERVER['HTTPS'] = true;

        $this->assertEquals(
            expected: 'https',
            actual: $this->path->getProtocol()
        );

        unset($_SERVER['HTTP_X_FORWARDED_PROTO']);
    }

    /**
     * @covers ::loadServicesViewsAndModelsDirectories
     * @return void
     */
    public function testItShouldLoadServicesViewsAndModelsDirectories(
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
        // ----- prepare plugin directories -----
        mkdir($tmpDir . '/vendor/carlonicora/minimalism-service-auth/src/Models',  0777, true);
        mkdir($tmpDir . '/vendor/carlonicora/minimalism-service-auth/src/Views',  0777, true);
        mkdir($tmpDir . '/vendor/carlonicora/minimalism-service-guards/src/Models',  0777, true);
        mkdir($tmpDir . '/vendor/carlonicora/minimalism-service-redis/src/Views',  0777, true);
        // ----- prepare internal directories -----
        mkdir($tmpDir . '/src/Services/Parser/Models',  0777, true);
        mkdir($tmpDir . '/src/Services/Database/Models',  0777, true);
        mkdir($tmpDir . '/src/Services/Database/Views');
        // ----------------------------------------
        $this->setProperty(
            object: $this->path,
            parameterName: 'root',
            parameterValue: $tmpDir
        );

        $this->invokeMethod(
            object: $this->path,
            methodName: 'loadServicesViewsAndModelsDirectories'
        );

        $this->assertCount(
            expectedCount: 4,
            haystack: $this->path->getServicesModelsDirectories(),
        );
        $this->assertContains(
            needle: $tmpDir . '/vendor/carlonicora/minimalism-service-guards/src/Models',
            haystack: $this->path->getServicesModelsDirectories()
        );
        $this->assertContains(
            needle: $tmpDir . '/vendor/carlonicora/minimalism-service-auth/src/Models',
            haystack: $this->path->getServicesModelsDirectories()
        );
        $this->assertContains(
            needle: $tmpDir . '/src/Services/Parser/Models',
            haystack: $this->path->getServicesModelsDirectories()
        );
        $this->assertContains(
            needle: $tmpDir . '/src/Services/Database/Models',
            haystack: $this->path->getServicesModelsDirectories()
        );

        $this->assertCount(
            expectedCount: 3,
            haystack: $this->path->getServicesViewsDirectories(),
        );
        $this->assertContains(
            needle: $tmpDir . '/vendor/carlonicora/minimalism-service-auth/src/Views',
            haystack: $this->path->getServicesViewsDirectories()
        );
        $this->assertContains(
            needle: $tmpDir . '/vendor/carlonicora/minimalism-service-redis/src/Views',
            haystack: $this->path->getServicesViewsDirectories()
        );
        $this->assertContains(
            needle: $tmpDir . '/src/Services/Database/Views',
            haystack: $this->path->getServicesViewsDirectories()
        );

        self::recurseRmdir($tmpDir);
    }

    /**
     * @covers ::getBaseInterface
     * @return void
     */
    public function testItShouldGetBaseInterface(
    ): void
    {
        $this->assertNull(Path::getBaseInterface());
    }
}