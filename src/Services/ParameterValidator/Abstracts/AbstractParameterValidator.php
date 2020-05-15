<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Abstracts;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\ParameterValidatorInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use Exception;
use RuntimeException;

abstract class AbstractParameterValidator implements ParameterValidatorInterface
{
    /** @var ParameterObject  */
    protected ParameterObject $object;

    /**
     * AbstractParameterValidator constructor.
     * @param ParameterObject $object
     */
    final public function __construct(ParameterObject $object)
    {
        $this->object = $object;
    }

    /**
     * @param ModelInterface $model
     * @param array $passedParameters
     * @throws Exception
     */
    final public function renderParameter(ModelInterface $model, array $passedParameters) : void
    {
        if (array_key_exists($this->object->parameterIdentifier, $passedParameters)) {
            $model->addReceivedParameters($this->object->parameterName);
            if ($passedParameters[$this->object->parameterIdentifier] !== null) {
                if ($this->object->isEncrypted) {
                   $model->setParameter($this->object->parameterName, $model->decrypter()->decryptParameter($passedParameters[$this->object->parameterIdentifier]));
                } else {
                    $this->setParameter($model, $passedParameters[$this->object->parameterIdentifier]);
                }
            }
        } elseif ($this->object->isRequired){
            throw new RuntimeException('Required parameter ' . $this->object->parameterIdentifier . ' missing.', 412);
        }
    }

    /**
     * @param ModelInterface $model
     * @param string $parameter
     * @throws Exception
     */
    abstract public function setParameter(ModelInterface $model, string $parameter) : void;
}