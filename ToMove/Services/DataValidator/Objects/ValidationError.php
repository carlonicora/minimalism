<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Objects;

use CarloNicora\Minimalism\Services\DataValidator\Enums\ValidationErrors;
use CarloNicora\Minimalism\Services\DataValidator\Enums\ValidatorTypes;

class ValidationError
{
    public function __construct(
        private ValidationErrors $error,
        private string $description,
        private ValidatorTypes $validatorType,
    )
    {
    }

    /**
     * @return ValidationErrors
     */
    public function getError(): ValidationErrors
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return ValidatorTypes
     */
    public function getValidatorType(): ValidatorTypes
    {
        return $this->validatorType;
    }
}