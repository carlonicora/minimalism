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

    /**
     * AbstractParameterValidator constructor.
     * @param ServicesFactory $services
     */
    final public function __construct(ServicesFactory $services)
    {
        $this->services = $services;
    }

    /**
     * @param ParameterObject $object
     * @param ModelInterface $model
     * @param array $passedParameters
     * @throws Exception
     */
    final public function renderParameter(ParameterObject $object, ModelInterface $model, array $passedParameters) : void
    {
        if (array_key_exists($object->parameterIdentifier, $passedParameters)) {
            $model->addReceivedParameters($object->parameterName);
            if ($passedParameters[$object->parameterIdentifier] !== null) {
                if ($object->isEncrypted) {
                   $model->setParameter($object->parameterName, $model->decrypter()->decryptParameter($passedParameters[$object->parameterIdentifier]));
                } else {
                    $this->setParameter($object, $model, $passedParameters[$object->parameterIdentifier]);
                }
            }
        } elseif ($object->isRequired){
            $this->services->logger()->error()->log(
                MinimalismErrorEvents::REQUIRED_PARAMETER_MISSING($object->parameterIdentifier)
            )->throw(Exception::class);
        }
    }

    /**
     * @param ParameterObject $object
     * @param ModelInterface $model
     * @param mixed $parameter
     * @throws Exception
     */
    abstract public function setParameter(ParameterObject $object, ModelInterface $model, $parameter) : void;
}