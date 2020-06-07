<?php
namespace CarloNicora\Minimalism\Core\JsonApi\Validators;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use Exception;

class JsonApiValidator extends AbstractParameterValidator
{
    /**
     * @param ParameterObject $object
     * @param ModelInterface $model
     * @param mixed $parameter
     * @throws Exception
     */
    public function setParameter(ParameterObject $object, ModelInterface $model, $parameter): void
    {
        $document = new Document($parameter);
        $model->setParameter($object->parameterName, $document);
    }
}