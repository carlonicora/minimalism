<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use DateTime;
use Exception;

class DateTimeValidator extends AbstractParameterValidator
{
    public function setParameter(ModelInterface $model, $parameter): void
    {
        if (strpos($parameter, '-') === false) {
            $parameter = '@' . $parameter;
        }

        try {
            $passedParameter = new DateTime($parameter);
            $model->setParameter($this->object->parameterName, $passedParameter);
        } catch (Exception $e) {
            $this->services->logger()->error()->log(
                MinimalismErrorEvents::PARAMETER_TYPE_MISMATCH($this->object->parameterIdentifier)
            )->throw(Exception::class);
        }
    }
}
