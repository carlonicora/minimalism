<?php

namespace CarloNicora\Minimalism\Tests\Unit\Modules\Web\Extensions;

use CarloNicora\Minimalism\Modules\Web\Extensions\TwigExtensions;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Twig\TwigFunction;

class TwigExtensionsTest extends AbstractTestCase
{

    public function testGetFunctions()
    {
        $instance = new TwigExtensions();

        $twigFunctions = $instance->getFunctions();
        $this->assertCount(2, $twigFunctions);
        $this->assertInstanceOf(TwigFunction::class, $twigFunctions[0]);
        $this->assertInstanceOf(TwigFunction::class, $twigFunctions[1]);
    }

    public function testIncluded()
    {
        $instance = new TwigExtensions();

        $element1 = ['id' => '1', 'type' => 'test'];
        $elements = [ $element1 ];

        $object = ['type' => 'test', 'id' => '1', 'extra-data' => 'x'];
        $this->assertEquals($element1, $instance->included($elements, $object));
    }

    public function testIncludedTypeId()
    {
        $instance = new TwigExtensions();

        $element1 = ['id' => '1', 'type' => 'test'];
        $elements = [ $element1 ];
        // matching id
        $this->assertEquals([], $instance->includedTypeId($elements, 'test-', '1'));
        // matching type
        $this->assertEquals([], $instance->includedTypeId($elements, 'test', '2'));
        // matching type and id
        $this->assertEquals($element1, $instance->includedTypeId($elements, 'test', '1'));
    }
}
