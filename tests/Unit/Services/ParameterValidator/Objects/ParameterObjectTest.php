<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Objects;

use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

/**
 * Class ParameterObjectTest
 * @package CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Objects
 *
 * The other test cases are capture in tests that use the ParameterObject for testing purposes
 */
class ParameterObjectTest extends AbstractTestCase
{

    public function testWithParameterName()
    {
        $instance = new ParameterObject('test1', ['name' => 'test2']);

        $this->assertEquals('test2', $instance->parameterName);
    }
}
