<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;

class StringValidator extends AbstractParameterValidator
{
    public function setParameter(ModelInterface $model, $parameter): void
    {
        /**
         * @todo typecast $parameter to string to be consistent with the other validators?
         */
        $model->setParameter($this->object->parameterName, $parameter);
    }
}
