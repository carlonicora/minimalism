<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Validators\BoolValidator;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;


class BoolValidatorTest extends AbstractTestCase
{

    /**
     * @dataProvider provider
     * @param $output
     * @param $input
     */
    public function testTransformValue($output, $input)
    {
        $instance = new BoolValidator($this->getServices());
        $this->assertEquals($output, $instance->transformValue($input));
    }


    public function provider()
    {
        return [
            [ false, "" ],
            [ false, null ],
            [ false, [] ],
            [ false, 0 ],
            [ false, "0" ],
            [ true,  ['a','b'] ],
            [ true,  1 ],
            [ true,  -1 ],
            [ true,  "1" ],
            [ true,  "-1" ]
        ];
    }
}
