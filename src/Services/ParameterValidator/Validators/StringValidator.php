<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;

class StringValidator extends AbstractParameterValidator
{
    /**
     * @param $value
     * @return string
     */
    public function transformValue($value): string
    {
        return (string)$value;
    }
}