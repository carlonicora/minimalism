<?php
namespace CarloNicora\Minimalism\Tests\Unit\Services\Paths;

use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use Exception;
use JsonException;

class PathsTest extends AbstractTestCase
{
    public function testRoot() : void
    {
        $this->assertEquals('http:///', $this->services->paths()->getUrl());
    }

    public function testLog() : void
    {
        $log = $this->services->paths()->getLog();
        $this->assertEquals('/data/logs/minimalism/', substr($log, -22));
    }

    /**
     * @throws JsonException
     */
    public function testNamespace() : void
    {
        $this->assertEquals('CarloNicora\\Minimalism\\', $this->services->paths()->getNamespace());
    }

    public function testFailGetModelsFolderNoComposer() : void
    {
        $this->setProperty($this->services->paths(), 'root', './tests/');

        $this->expectExceptionCode(500);

        $this->services->paths()->getModelsFolder();
    }

    public function testFailGetModelsFolderWrongComposer() : void
    {
        $this->setProperty($this->services->paths(), 'root', './tests/Unit/Mocks/WrongComposer');

        $this->expectExceptionCode(500);

        $this->services->paths()->getModelsFolder();
    }

    public function testFailGetModelsFolderComposerNoNamespace() : void
    {
        $this->setProperty($this->services->paths(), 'root', './tests/Unit/Mocks/ComposerNoNamespace');

        $this->expectExceptionCode(1005);

        $this->services->paths()->getModelsFolder();
    }

    /**
     * @throws Exception
     */
    public function testFailInitialiseDirectoryStructure() : void
    {
        $this->setProperty($this->services->paths(), 'root', '~/etc/s');

        $this->expectExceptionCode(500);

        $this->services->paths()->initialiseDirectoryStructure();
    }
}