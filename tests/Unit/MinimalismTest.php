<?php
namespace CarloNicora\Minimalism\Tests\Unit;

use CarloNicora\Minimalism\Minimalism;
use PHPUnit\Framework\TestCase;

class MinimalismTest extends TestCase
{
    /** @var Minimalism|null  */
    private ?Minimalism $minimalism=null;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->minimalism = new Minimalism();
    }

    /**
     *
     */
    public function testInitialise(): void
    {
        self::assertNotNull($this->minimalism);
    }
}