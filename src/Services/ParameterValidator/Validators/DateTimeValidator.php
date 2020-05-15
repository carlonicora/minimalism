<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Validators;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use DateTime;
use Exception;
use RuntimeException;

class DateTimeValidator extends AbstractParameterValidator
{
    public function setParameter(ModelInterface $model, string $parameter): void
    {
        if (strpos($parameter, '-') === false) {
            $parameter = '@' . $parameter;
        }

        try {
            $passedParameter = new DateTime($parameter);
            $model->setParameter($this->object->parameterName, $passedParameter);
        } catch (Exception $e) {
            throw new RuntimeException('Parameter ' . $this->object->parameterIdentifier . ' is invalid', 412);
        }
    }
}