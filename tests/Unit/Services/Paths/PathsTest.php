<?php
namespace CarloNicora\Minimalism\Tests\Unit\Services\Paths;

use CarloNicora\Minimalism\Services\Paths\Configurations\PathsConfigurations;
use CarloNicora\Minimalism\Services\Paths\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\Paths\Paths;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;

class PathsTest extends AbstractTestCase
{
    /**
     * @throws Exception
     */
    public function testServiceCreation() : Paths
    {
        $service = new ServiceFactory($this->getServices());
        $config = new PathsConfigurations();
        $services = $this->getServices();
        $paths = new Paths($config, $services);

        self::assertEquals($paths, $service->create($services));

        return $paths;
    }

    /**
     * @param Paths $paths
     * @depends testServiceCreation
     * @throws Exception
     */
    public function testGetCorrectModelFolder(Paths $paths) : void
    {
        $this->setProperty($paths, 'root', './tests/Mocks/ValidComposerNamespace');
        self::assertEquals('./tests/Mocks/ValidComposerNamespace/src/Models/', $paths->getModelsFolder());
    }

    /**
     * @throws Exception
     */
    public function testFailGetModelsFolderNoComposer() : void
    {
        $this->expectExceptionCode(500);
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests/');

        $paths->getModelsFolder();
    }

    /**
     * @throws Exception
     */
    public function testFailGettingModelFolderInvalidJson() : void
    {
        $this->expectExceptionCode(500);
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests/Mocks/InvalidComposerJson');
        $paths->getModelsFolder();
    }

    /**
     * @throws Exception
     */
    public function testFailGettingModelFolderNoNamespace() : void
    {
        $this->expectExceptionCode(13);
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests/Mocks/NoNamespaceComposerJson');
        $paths->getModelsFolder();
    }

    public function testRoot() : void
    {
        self::assertEquals('http:///', $this->getServices()->paths()->getUrl());
    }

    public function testLog() : void
    {
        $log = $this->getServices()->paths()->getLog();
        self::assertEquals('/data/logs/minimalism/', substr($log, -22));
    }

    /**
     * @throws Exception
     */
    public function testNamespace() : void
    {
        $services = $this->getServices();
        $this->setProperty($services->paths(), 'root', './');

        self::assertEquals('CarloNicora\\Minimalism\\', $services->paths()->getNamespace());
    }

    /**
     * @throws Exception
     */
    public function testFailGetNamespaceNoComposer() : void
    {
        $this->expectExceptionCode(12);
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests/');

        $paths->getNamespace();
    }

    /**
     * @throws Exception
     */
    public function testFailGetNamespaceInvalidJson() : void
    {
        $this->expectExceptionCode(12);
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests/Mocks/InvalidComposerJson');

        $paths->getNamespace();
    }

    /**
     * @throws Exception
     */
    public function testFailGetNamespaceNoNamespace() : void
    {
        $this->expectExceptionCode(13);
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests/Mocks/NoNamespaceComposerJson');

        $paths->getNamespace();
    }

    /**
     * @throws Exception
     */
    public function testFailInitialiseDirectoryStructure() : void
    {
        $this->expectExceptionCode(500);
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', '~/etc/s');
        $paths->initialiseDirectoryStructure();
    }

    public function testSetUrlVersion() : void
    {
        $paths = $this->getServices()->paths();
        $paths->setUrlVersion('v1.0');
        self::assertEquals('http:///v1.0/', $paths->getUrl());
    }

    public function testGetCache() : void
    {
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests');
        self::assertEquals('./tests/data/cache/services.cache', $paths->getCache());
    }

    public function testGetRoot() : void
    {
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests');
        self::assertEquals('./tests', $paths->getRoot());
    }
}