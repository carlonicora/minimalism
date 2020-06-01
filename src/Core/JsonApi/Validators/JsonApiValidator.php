<?php
namespace CarloNicora\Minimalism\Core\JsonApi\Validators;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;

class JsonApiValidator extends AbstractParameterValidator
{
    public function setParameter(ModelInterface $model, $parameter): void
    {
        $document = new Document($parameter);
        $model->setParameter($this->object->parameterName, $document);
    }
}