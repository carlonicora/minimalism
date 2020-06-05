<?php
namespace CarloNicora\Minimalism\Core\JsonApi\Validators;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;

class JsonApiValidator extends AbstractParameterValidator
{
    /**
     * @param ModelInterface $model
     * @param mixed $parameter
     * @throws \Exception
     */
    public function setParameter(ModelInterface $model, $parameter): void
    {
        if (false === (\is_null($parameter) || \is_array($parameter))) {
            throw new \InvalidArgumentException('JsonApiValidator $parameter must be of type array');
        }

        $document = new Document($parameter);
        $model->setParameter($this->object->parameterName, $document);
    }
}
