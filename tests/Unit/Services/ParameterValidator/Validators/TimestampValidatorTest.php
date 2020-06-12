<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\TimestampValidator;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;
use function date;

class TimestampValidatorTest extends AbstractTestCase
{

    /**
     * @note very much similar to the DateTimeValidatorTest, as such the method is duplicated
     * @throws Exception
     */
    public function testSetParameter()
    {
        $parameterName = 'test';
        $dateValue = date('Y-m-d H:i:s');
        $dateValueHoursMinutesSeconds = date('H:i:s');

        $instance = new TimestampValidator($this->getServices());

        $mock = $this->getMockBuilder(TestModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();

        $mock->expects($this->at(0))->method('setParameter')->with(
            $this->identicalTo($parameterName),
            $this->callback(function($timestamp) use($dateValue) {
                return date('Y-m-d H:i:s', $timestamp) === $dateValue;
            })
        );


        $instance->setParameter(new ParameterObject($parameterName, []), $mock, $dateValue);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Parameter Type mismatch: $parameterName");
        $instance->setParameter(new ParameterObject($parameterName, []), $mock, $dateValueHoursMinutesSeconds);
    }
}
