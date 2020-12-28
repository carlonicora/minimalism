<?php
namespace CarloNicora\Minimalism\Tests;

use CarloNicora\Minimalism\Minimalism;
use PHPUnit\Framework\TestCase;

class MinimalismTest extends TestCase
{
    public function testNotTest(): void
    {
        $minimalism = new Minimalism();
        self::assertEquals(1,1);
    }
}