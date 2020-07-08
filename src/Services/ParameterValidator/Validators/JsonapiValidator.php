<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use Exception;

class JsonapiValidator extends AbstractParameterValidator
{
    /**
     * @param $value
     * @return Document|null
     * @throws Exception
     */
    public function transformValue($value) : ?Document
    {
        if ($value === null) {
            return null;
        }

        return new Document($value);
    }
}