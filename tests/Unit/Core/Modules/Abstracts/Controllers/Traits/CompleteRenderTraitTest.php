<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers\Traits;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\Traits\CompleteRenderTrait;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\TestCase;

class CompleteRenderTraitTest extends AbstractTestCase
{

    public function testSaveCacheCallsPersistAtPath()
    {
        $mock = $this->getMockBuilder(CompleteRenderTrait::class)
            ->onlyMethods(['persistAtPath'])
            ->getMockForTrait();
        $mock->expects($this->exactly(300))->method('persistAtPath');

        foreach (\range(100, 399) as $statusCode) {
            $mock->saveCache($this->getServices(), $statusCode);
        }
    }

    public function testSaveCacheDoesntCallPersistAtPath()
    {
        $mock = $this->getMockBuilder(CompleteRenderTrait::class)
            ->onlyMethods(['persistAtPath'])
            ->getMockForTrait();
        $mock->expects($this->never())->method('persistAtPath');

        foreach (range(400, 599) as $statusCode) {
            $mock->saveCache($this->getServices(), $statusCode);
        }
    }


    public function testSaveCacheCapturesPeristAtPathException()
    {
        $mock = $this->getMockBuilder(CompleteRenderTrait::class)
            ->onlyMethods(['persistAtPath'])
            ->getMockForTrait();
        $mock->expects($this->once())->method('persistAtPath')->willThrowException(new \Exception('Test exception'));

        $mock->saveCache($this->getServices(), 200);
    }
}
