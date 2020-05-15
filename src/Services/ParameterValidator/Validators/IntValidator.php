<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;

class IntValidator extends AbstractParameterValidator
{
    public function setParameter(ModelInterface $model, string $parameter): void
    {
        $model->setParameter($this->object->parameterName, (int)$parameter);
    }
}