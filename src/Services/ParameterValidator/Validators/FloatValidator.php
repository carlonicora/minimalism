<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;

class FloatValidator extends AbstractParameterValidator
{
    /**
     * @param $value
     * @return float
     */
    public function transformValue($value): float
    {
        return (float)$value;
    }
}