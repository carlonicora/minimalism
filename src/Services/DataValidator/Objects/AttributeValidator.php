<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Objects;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\ResourceObject;
use CarloNicora\Minimalism\Services\DataValidator\Abstracts\AbstractValidator;
use CarloNicora\Minimalism\Services\DataValidator\Enums\DataTypes;
use CarloNicora\Minimalism\Services\DataValidator\Enums\ValidationErrors;
use CarloNicora\Minimalism\Services\DataValidator\Enums\ValidatorTypes;
use Exception;

class AttributeValidator extends AbstractValidator
{
    /**
     * @param string $name
     * @param bool $isRequired
     * @param DataTypes $type
     */
    public function __construct(
        string $name,
        private bool $isRequired=false,
        private DataTypes $type=DataTypes::string,
    )
    {
        $this->name = $name;
    }

    /**
     * @param Document|ResourceObject $resource
     * @return bool
     * @throws Exception
     */
    public function validate(
        Document|ResourceObject $resource,
    ): bool
    {
        $hasAttribute = $resource->attributes->has($this->name);

        if ($this->isRequired && !$hasAttribute){
            $this->setValidationError(
                new ValidationError(
                    error: ValidationErrors::attributeMissing,
                    description: $this->name,
                    validatorType: ValidatorTypes::attribute,
                )
            );
            return false;
        }

        if ($hasAttribute) {
            $attributeValue = $resource->attributes->get($this->name);

            if (! $this->isRequired && $attributeValue === null) {
                return true;
            }

            $type = DataTypes::tryFrom(gettype($attributeValue));
            if ($this->type !== $type){
                $this->setValidationError(
                    new ValidationError(
                        error: ValidationErrors::typeMismatch,
                        description: $this->name . ' (expected: ' . $this->type->value . ' actual: ' . $type?->value . ')',
                        validatorType: ValidatorTypes::attribute,
                    )
                );
                return false;
            }
        }

        return true;
    }
}