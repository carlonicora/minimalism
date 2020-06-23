<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\FloatValidator;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

class FloatValidatorTest extends AbstractTestCase
{

    /**
     * @dataProvider provider
     * @param $output
     * @param $input
     * @throws Exception
     */
    public function testSetParameter($output, $input)
    {
        $parameterName = 'test';

        $instance = new FloatValidator($this->getServices());

        /** @var MockObject|ModelInterface $mock */
        $mock = $this->getMockBuilder(TestModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();
        $mock->expects($this->once())->method('setParameter')->with($parameterName, $this->identicalTo($output));

        $instance->setParameter(new ParameterObject($parameterName, []), $mock, $input);
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
