<?php

namespace CarloNicora\Minimalism\Tests\Unit\Parameters;

use CarloNicora\Minimalism\Parameters\PositionedParameter;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;

/**
 * Class PositionedParameterTest
 * @package CarloNicora\Minimalism\Tests\Unit\Parameters
 * @coversDefaultClass \CarloNicora\Minimalism\Parameters\PositionedParameter
 */
class PositionedParameterTest extends AbstractTestCase
{
    /**
     * @covers ::getValue
     * @return void
     */
    public function testItShouldGetValue(
    ): void
    {
        $parameter = new PositionedParameter('someValue');

        $this->assertEquals(
            expected: 'someValue',
            actual: $parameter->getValue()
        );
    }
}