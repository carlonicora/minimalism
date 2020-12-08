<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Validators\FloatValidator;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class FloatValidatorTest extends AbstractTestCase
{

    /**
     * @dataProvider provider
     * @param $output
     * @param $input
     */
    public function testTransformValue($output, $input)
    {
        $instance = new FloatValidator($this->getServices());

        self::assertSame($output, $instance->transformValue($input));
    }


    public function provider()
    {
        return [
            [ 0.0, "" ],
            [ 0.0, null ],
            [ 0.0, [] ],
            [ 0.0, 0 ],
            [ 0.0, "0" ],
            [ 1.0,  ['a','b'] ],
            [ 1.0,  1 ],
            [ -1.0,  -1 ],
            [ 1.0,  "1" ],
            [ -1.0,  "-1" ]
        ];
    }
}
