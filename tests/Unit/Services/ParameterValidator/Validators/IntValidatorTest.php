<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Validators\IntValidator;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class IntValidatorTest extends AbstractTestCase
{

    /**
     * @dataProvider provider
     * @param $output
     * @param $input
     */
    public function testTransformValue($output, $input)
    {
        $instance = new IntValidator($this->getServices());

        $this->assertSame($output, $instance->transformValue($input));
    }


    public function provider()
    {
        return [
            [ 0, "" ],
            [ 0, null ],
            [ 0, [] ],
            [ 0, 0 ],
            [ 0, "0" ],
            [ 1,  ['a','b'] ],
            [ 1,  1 ],
            [ -1,  -1 ],
            [ 1,  "1" ],
            [ -1,  "-1" ]
        ];
    }
}
