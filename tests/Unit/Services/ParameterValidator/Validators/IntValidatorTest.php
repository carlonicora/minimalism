<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\IntValidator;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class IntValidatorTest extends AbstractTestCase
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

        $instance = new IntValidator($this->getServices());

        $mock = $this->getMockBuilder(TestModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();
        $mock->expects($this->once())->method('setParameter')->with($parameterName, $this->identicalTo($output));

        $instance->setParameter(new ParameterObject($parameterName, []), $mock, $input);
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
