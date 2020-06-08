<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\StringValidator;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class StringValidatorTest extends AbstractTestCase
{

    /**
     * @dataProvider provider
     * @param $output
     * @param $input
     * @throws \Exception
     */
    public function testSetParameter($output, $input)
    {
        $parameterName = 'test';

        $instance = new StringValidator($this->getServices(), new ParameterObject($parameterName, []));

        $mock = $this->getMockBuilder(TestModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();
        $mock->expects($this->once())->method('setParameter')->with($parameterName, $this->identicalTo($output));

        // does not typecast input to string
        $instance->setParameter($mock, $input);
    }


    public function provider()
    {
        return [
            [ "", "" ],
            [ null, null ],
            [ [], [] ],
            [ 0, 0 ],
            [ "0", "0" ],
            [ ['a', 'b'],  ['a','b'] ],
            [ 1,  1 ],
            [ -1,  -1 ],
            [ "1",  "1" ],
            [ "-1",  "-1" ]
        ];
    }
}
