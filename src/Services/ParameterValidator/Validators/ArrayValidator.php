<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;

class ArrayValidator extends AbstractParameterValidator
{
    /**
     * @param $value
     * @return array|null
     */
    public function transformValue($value) : ?array
    {
        if ($value === null) {
            return null;
        }

        return (array)$value;
    }
}