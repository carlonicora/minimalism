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

        $this->assertEquals($paths, $service->create($services));

        return $paths;
    }

    /**
     * @param Paths $paths
     * @depends testServiceCreation
     * @throws Exception
     */
    public function testGetCorrectModelFolder(Paths $paths) : void
    {
        $this->assertEquals('/opt/project/src/Models/', $paths->getModelsFolder());
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
        $this->expectExceptionCode(500);
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests/Mocks/NoNamespaceComposerJson');
        $paths->getModelsFolder();
    }

    public function testRoot() : void
    {
        $this->assertEquals('http:///', $this->getServices()->paths()->getUrl());
    }

    public function testLog() : void
    {
        $log = $this->getServices()->paths()->getLog();
        $this->assertEquals('/data/logs/minimalism/', substr($log, -22));
    }

    /**
     * @throws Exception
     */
    public function testNamespace() : void
    {
        $this->assertEquals('CarloNicora\\Minimalism\\', $this->getServices()->paths()->getNamespace());
    }

    /**
     * @throws Exception
     */
    public function testFailGetNamespaceNoComposer() : void
    {
        $this->expectExceptionCode(500);
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests/');

        $paths->getNamespace();
    }

    /**
     * @throws Exception
     */
    public function testFailGetNamespaceInvalidJson() : void
    {
        $this->expectExceptionCode(500);
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests/Mocks/InvalidComposerJson');

        $paths->getNamespace();
    }

    /**
     * @throws Exception
     */
    public function testFailGetNamespaceNoNamespace() : void
    {
        $this->expectExceptionCode(500);
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
        $this->assertEquals('http:///v1.0/', $paths->getUrl());
    }

    public function testGetCache() : void
    {
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests');
        $this->assertEquals('./tests/data/cache/services.cache', $paths->getCache());
    }

    public function testGetRoot() : void
    {
        $paths = $this->getServices()->paths();
        $this->setProperty($paths, 'root', './tests');
        $this->assertEquals('./tests', $paths->getRoot());
    }
}