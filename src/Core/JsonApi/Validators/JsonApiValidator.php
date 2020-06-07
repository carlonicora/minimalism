<?php
namespace CarloNicora\Minimalism\Core\JsonApi\Validators;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use Exception;

class JsonApiValidator extends AbstractParameterValidator
{
    /**
     * @param $value
     * @return Document
     * @throws Exception
     */
    public function transformValue($value): Document
    {
        return new Document($value);
    }
}