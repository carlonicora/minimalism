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
        if ($value === null){
            return 0;
        }

        return (float)$value;
    }
}