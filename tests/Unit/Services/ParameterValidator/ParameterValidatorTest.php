<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceConfigurationsInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Configurations\ParameterValidatorConfigurations;
use CarloNicora\Minimalism\Services\ParameterValidator\ParameterValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\BoolValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\DateTimeValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\FloatValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\IntValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\StringValidator;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;


class ParameterValidatorTest extends AbstractTestCase
{

    /**
     * @throws Exception
     */
    public function testValidate()
    {
        /** @var MockObject|ServiceConfigurationsInterface $mock */
        $mockServiceConfigurations = $this->getMockBuilder(ParameterValidatorConfigurations::class)
            ->getMock();

        /** @noinspection PhpParamsInspection */
        $instance = new ParameterValidator($mockServiceConfigurations, $this->getServices());

        /** @var MockObject|ModelInterface $mockModel */
        $mockModel = $this->getMockBuilder(TestModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->onlyMethods(['getParameters', 'setParameter'])
            ->getMock();

        $mockModel->expects(self::once())->method('getParameters')->willReturn([
            'int_value' => [ 'validator' => IntValidator::class ],
            'float_value' => [ 'validator' => FloatValidator::class ],
            'bool_value' => [ 'validator' => BoolValidator::class ],
            'string_value' => [ 'validator' => StringValidator::class ],
            'date_value' => [ 'validator' => DateTimeValidator::class ]
        ]);

        $mockModel->expects(self::at(1))->method('setParameter')->with('int_value', 1);
        $mockModel->expects(self::at(2))->method('setParameter')->with('float_value', 1.0);
        $mockModel->expects(self::at(3))->method('setParameter')->with('bool_value', true);
        $mockModel->expects(self::at(4))->method('setParameter')->with('string_value', '1');
        $mockModel->expects(self::at(5))->method('setParameter')->with('date_value', self::callback(function($date) {
            return $date === '2020-06-06 00:00:00';
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
