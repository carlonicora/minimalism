<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Interfaces;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;

interface ParameterValidatorInterface
{
    /**
     * ParameterValidatorInterface constructor.
     * @param ServicesFactory $services
     */
    public function __construct(ServicesFactory $services);

    /**$object
     * @param ParameterObject $object
     * @param ModelInterface $model
     * @param array $passedParameters
     * @return mixed
     */
    public function renderParameter(ParameterObject $object, ModelInterface $model, array $passedParameters);
}