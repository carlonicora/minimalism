<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator;

use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceConfigurationsInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\ParameterValidatorFactoryInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\ParameterValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\BoolValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\DateTimeValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\FloatValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\IntValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\StringValidator;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;


class ParameterValidatorTest extends AbstractTestCase
{

    public function testValidate()
    {
        $mockServiceConfigurations = $this->getMockBuilder(ServiceConfigurationsInterface::class)
            ->getMock();

        $instance = new ParameterValidator($mockServiceConfigurations, $this->getServices());

        $mockModel = $this->getMockBuilder(TestModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->onlyMethods(['getParameters', 'setParameter'])
            ->getMock();

        $mockModel->expects($this->once())->method('getParameters')->willReturn([
            'int_value' => [ 'validator' => IntValidator::class ],
            'float_value' => [ 'validator' => FloatValidator::class ],
            'bool_value' => [ 'validator' => BoolValidator::class ],
            'string_value' => [ 'validator' => StringValidator::class ],
            'date_value' => [ 'validator' => DateTimeValidator::class ]
        ]);

        $mockModel->expects($this->at(1))->method('setParameter')->with('int_value', 1);
        $mockModel->expects($this->at(2))->method('setParameter')->with('float_value', 1.0);
        $mockModel->expects($this->at(3))->method('setParameter')->with('bool_value', true);
        $mockModel->expects($this->at(4))->method('setParameter')->with('string_value', '1');
        $mockModel->expects($this->at(5))->method('setParameter')->with('date_value', $this->callback(function($date) {
            return $date->format('Y-m-d H:i:s') === '2020-06-06 00:00:00';
        }));

        $instance->validate($mockModel, [
            'int_value' => '1',
            'float_value' => '1',
            'bool_value' => '1',
            'string_value' => '1',
            'date_value' => '2020-06-06 00:00:00'
        ]);
    }
}
