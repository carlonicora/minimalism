<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use DateTime;
use Exception;
use RuntimeException;

class TimestampValidator extends AbstractParameterValidator
{
    public function setParameter(ModelInterface $model, string $parameter): void
    {
        try {
            if (strpos($parameter, '-') !== false) {
                $date = new DateTime($parameter);
            } else {
                $date = new DateTime('@' . $parameter);
            }
            $passedParameter = $date->getTimestamp();
        } catch (Exception $e) {
            throw new RuntimeException('Parameter ' . $this->object->parameterIdentifier . ' is invalid', 412, $e);
        }

        $model->setParameter($this->object->parameterName, $passedParameter);
    }
}