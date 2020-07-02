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
     * @return string|null
     * @throws Exception
     */
    public function transformValue($value) : ?string
    {
        if ($value === null){
            return null;
        }

        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_string($value)) {
            if (strpos($value, '-') === false) {
                return date('Y-m-d H:i:s', $value);
            }

            return $value;
        }

        return $value;
    }


}
