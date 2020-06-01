<?php
namespace CarloNicora\Minimalism\Tests\Unit\Services\ParametersValidator\Configurations;

use CarloNicora\Minimalism\Services\ParameterValidator\Configurations\ParameterValidatorConfigurations;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class ParametersValidatorConfigurationsTest extends AbstractTestCase
{
    public function testDefaultConfigurations() : void
    {
        $config = new ParameterValidatorConfigurations();
        $this->assertInstanceOf(ParameterValidatorConfigurations::class, $config);
    }
}