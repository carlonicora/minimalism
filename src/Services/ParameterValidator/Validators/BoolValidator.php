<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;

class BoolValidator extends AbstractParameterValidator
{
    /**
     * @param ParameterObject $object
     * @param ModelInterface $model
     * @param mixed $parameter
     */
    public function setParameter(ParameterObject $object, ModelInterface $model, $parameter): void
    {
        $model->setParameter($object->parameterName, (bool)$parameter);
    }
}