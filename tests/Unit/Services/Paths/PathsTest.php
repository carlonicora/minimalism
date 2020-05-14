<?php
namespace CarloNicora\Minimalism\Tests\Unit\Services\Paths;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Paths\Paths;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;

class PathsTest extends AbstractTestCase
{
    /** @var Paths  */
    private Paths $path;

    public function setUp(): void
    {
        parent::setUp();

        $services = new ServicesFactory();
        $this->path = $services->service(Paths::class);
    }

    public function testRoot() : void
    {
        $this->assertEquals('http:///', $this->path->getUrl());
    }

    public function testLog() : void
    {
        $this->assertEquals('/opt/project/data/logs/minimalism', $this->path->getLog());
    }

    public function testNamespace() : void
    {
        $this->assertEquals('CarloNicora\\Minimalism\\', $this->path->getNamespace());
    }


}