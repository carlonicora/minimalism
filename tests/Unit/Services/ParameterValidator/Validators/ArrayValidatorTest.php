<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Validators\ArrayValidator;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class ArrayValidatorTest extends AbstractTestCase
{

    /**
     * @dataProvider provider
     * @param $output
     * @param $input
     */
    public function testTransformValue($input, $output): void
    {
        $instance = new ArrayValidator($this->getServices());
        self::assertSame($output, $instance->transformValue($input));
    }

    public function provider()
    {
        return [
            [ "", [ "" ] ],
            [ null, null ],
            [ "0", [ "0" ] ],
            [ "1",  [ "1" ] ],
            [ "-1", [ "-1" ] ],
            [
                [ 'a' => 'b', 'c' => 'd' ],
                [ 'a' => 'b', 'c' => 'd' ]
            ],
            [
                (object)[ 'a' => 'b', 'c' => 'd' ],
                [ 'a' => 'b', 'c' => 'd' ]
            ]
        ];
    }
}
