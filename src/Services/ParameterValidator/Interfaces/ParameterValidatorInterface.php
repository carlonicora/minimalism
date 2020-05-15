<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Interfaces;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;

interface ParameterValidatorInterface
{
    /**
     * ParameterValidatorInterface constructor.
     * @param ParameterObject $object
     */
    public function __construct(ParameterObject $object);

    /**$object
     * @param ModelInterface $model
     * @param array $passedParameters
     * @return mixed
     */
    public function renderParameter(ModelInterface $model, array $passedParameters);
}