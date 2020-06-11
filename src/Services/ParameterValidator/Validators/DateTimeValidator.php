<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use DateTime;
use Exception;

class DateTimeValidator extends AbstractParameterValidator
{
    /**
    /**
     * @param $value
     * @return DateTime|null
     * @throws Exception
     */
    public function transformValue($value) : ?DateTime
    {
        if ($value === null){
            return null;
        }

        if (strpos($value, '-') === false) {
            $value = '@' . $value;
        }

        return new DateTime($value);
    }


}