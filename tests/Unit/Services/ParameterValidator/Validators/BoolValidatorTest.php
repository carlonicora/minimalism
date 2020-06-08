<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\BoolValidator;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;


class BoolValidatorTest extends AbstractTestCase
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

        $instance = new BoolValidator($this->getServices());

        $mock = $this->getMockBuilder(TestModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();
        $mock->expects($this->once())->method('setParameter')->with($parameterName, $this->identicalTo($output));

        $instance->setParameter(new ParameterObject($parameterName, []), $mock, $input);
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
