<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;

class IntValidator extends AbstractParameterValidator
{
    /**
     * @param $value
     * @return int
     */
    public function transformValue($value) : int
    {
        if ($value === null){
            return 0;
        }

        return (int)$value;
    }
}