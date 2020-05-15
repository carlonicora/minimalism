<?php
namespace CarloNicora\Minimalism\Tests\Unit\Services\Paths;

use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use JsonException;

class PathsTest extends AbstractTestCase
{
    public function testRoot() : void
    {
        $this->assertEquals('http:///', $this->services->paths()->getUrl());
    }

    public function testLog() : void
    {
        $this->assertEquals('/opt/project/data/logs/minimalism', $this->services->paths()->getLog());
    }

    /**
     * @throws JsonException
     */
    public function testNamespace() : void
    {
        $this->assertEquals('CarloNicora\\Minimalism\\', $this->services->paths()->getNamespace());
    }


}