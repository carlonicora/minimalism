<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;

class StringValidator extends AbstractParameterValidator
{
    /**
     * @param $value
     * @return string|null
     */
    public function transformValue($value): ?string
    {
        if ($value === null){
            return null;
        }

        return (string)$value;
    }
}
