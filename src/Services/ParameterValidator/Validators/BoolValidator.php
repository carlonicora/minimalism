<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;

class BoolValidator extends AbstractParameterValidator
{
    /**
     * @param $value
     * @return bool
     */
    public function transformValue($value) : bool
    {
        return (bool)$value;
    }
}