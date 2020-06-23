<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Factories;

use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Services\ParameterValidator\Factories\ParameterValidatorFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\StringValidator;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class ParameterValidatorFactoryTest extends AbstractTestCase
{

    public function testCreateParameterValidatorWithDefaults()
    {
        $instance = new ParameterValidatorFactory();
        $validator = $instance->createParameterValidator(
            $this->getServices(),
            StringValidator::class
        );

        $this->assertInstanceOf(StringValidator::class, $validator);
    }


    public function testCreateParameterValidatorWithInvalidValidatorClassname()
    {
        $instance = new ParameterValidatorFactory();

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Parameter Validator not found');

        $validator = $instance->createParameterValidator(
            $this->getServices(),
            'Class_does_not_exist'
        );
    }
}
