<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use DateTime;
use Exception;

class TimestampValidator extends AbstractParameterValidator
{
    /**
     * @param $value
     * @return int|mixed
     * @throws Exception
     */
    public function transformValue($value): int
    {
        if (strpos($value, '-') !== false) {
            $date = new DateTime($value);
        } else {
            $date = new DateTime('@' . $value);
        }
        return $date->getTimestamp();
    }
}