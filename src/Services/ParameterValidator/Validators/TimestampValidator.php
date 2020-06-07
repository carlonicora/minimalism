<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use DateTime;
use Exception;

class TimestampValidator extends AbstractParameterValidator
{
    /**
     * @param ParameterObject $object
     * @param ModelInterface $model
     * @param mixed $parameter
     * @throws Exception
     */
    public function setParameter(ParameterObject $object, ModelInterface $model, $parameter): void
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
                MinimalismErrorEvents::PARAMETER_TYPE_MISMATCH($object->parameterIdentifier)
            )->throw(Exception::class);
        }

        $model->setParameter($object->parameterName, $passedParameter);
    }
}