<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use DateTime;
use Exception;

class TimestampValidator extends AbstractParameterValidator
{
    public function setParameter(ModelInterface $model, $parameter): void
    {
        $passedParameter = '';

        try {
            if (strpos($parameter, '-') !== false) {
                $date = new DateTime($parameter);
            } else {
                $date = new DateTime('@' . $parameter);
            }
            $passedParameter = $date->getTimestamp();
        } catch (Exception $e) {
            $this->services->logger()->error()->log(
                MinimalismErrorEvents::PARAMETER_TYPE_MISMATCH($this->object->parameterIdentifier)
            )->throw(Exception::class);
        }

        $model->setParameter($this->object->parameterName, $passedParameter);
    }
}