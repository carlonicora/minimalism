<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Abstracts;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\ParameterValidatorInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use Exception;

abstract class AbstractParameterValidator implements ParameterValidatorInterface
{
    /**
     * @var ServicesFactory
     */
    protected ServicesFactory $services;

    /** @var ParameterObject  */
    protected ParameterObject $object;

    /**
     * AbstractParameterValidator constructor.
     * @param ServicesFactory $services
     * @param ParameterObject $object
     */
    final public function __construct(ServicesFactory $services, ParameterObject $object)
    {
        $this->services = $services;
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
            $this->services->logger()->error()->log(
                MinimalismErrorEvents::REQUIRED_PARAMETER_MISSING($this->object->parameterIdentifier)
            )->throw(Exception::class);
        }
    }

    /**
     * @param ModelInterface $model
     * @param mixed $parameter
     * @throws Exception
     */
    abstract public function setParameter(ModelInterface $model, $parameter) : void;
}