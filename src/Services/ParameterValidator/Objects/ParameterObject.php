<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Objects;

use CarloNicora\Minimalism\Services\ParameterValidator\ParameterValidator;

class ParameterObject
{
    /** @var string  */
    public string $parameterIdentifier;

    /** @var string  */
    public string $parameterName;

    /** @var bool  */
    public bool $isRequired=false;

    /** @var bool  */
    public bool $isEncrypted=false;

    /** @var string  */
    public string $validator=ParameterValidator::PARAMETER_TYPE_STRING;

    /**
     * ParameterObject constructor.
     * @param string $parameterIdentifier
     * @param array $parameter
     */
    public function __construct(string $parameterIdentifier, array $parameter)
    {
        $this->parameterIdentifier = $parameterIdentifier;

        if (!array_key_exists('name', $parameter)) {
            $this->parameterName = $parameterIdentifier;
        } else {
            $this->parameterName = $parameter['name'];
        }

        if (array_key_exists('required', $parameter)){
            $this->isRequired = (bool)$parameter['required'];
        }

        if (array_key_exists('encrypted', $parameter)){
            $this->isEncrypted = (bool)$parameter['encrypted'];
        }

        if (array_key_exists('validator', $parameter)){
            $this->validator = $parameter['validator'];
        }
    }
}