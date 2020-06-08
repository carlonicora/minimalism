<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\DateTimeValidator;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class DateTimeValidatorTest extends AbstractTestCase
{

    public function testSetParameter()
    {
        $parameterName = 'test';
        $dateValue = date('Y-m-d H:i:s');
        $dateValueHoursMinutesSeconds = \date('H:i:s');

        $instance = new DateTimeValidator($this->getServices(), new ParameterObject($parameterName, []));

        $mock = $this->getMockBuilder(TestModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMock();

        $mock->expects($this->at(0))->method('setParameter')->with(
            $this->identicalTo($parameterName),
            $this->callback(function(\DateTime $date) use($dateValue) {
                return $date->format('Y-m-d H:i:s') === $dateValue;
            })
        );


        $instance->setParameter($mock, $dateValue);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Parameter Type mismatch: $parameterName");
        $instance->setParameter($mock, $dateValueHoursMinutesSeconds);
    }
}
