<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Objects;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\ResourceObject;
use CarloNicora\Minimalism\Services\DataValidator\Abstracts\AbstractValidator;
use CarloNicora\Minimalism\Services\DataValidator\Enums\ValidationErrors;
use CarloNicora\Minimalism\Services\DataValidator\Enums\ValidatorTypes;
use Exception;

class ResourceValidator extends AbstractValidator
{
    /** @var AttributeValidator[]  */
    private array $attributesValidator=[];

    /**
     * @param string $type
     * @param bool $isIdRequired
     */
    public function __construct(
        private string $type,
        private bool $isIdRequired,
    )
    {
    }

    /**
     * @param AttributeValidator $validator
     */
    public function addAttributeValidator(
        AttributeValidator $validator,
    ): void
    {
        $this->attributesValidator[] = $validator;
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
        if ($resource->type !== $this->type){
            $this->setValidationError(
                new ValidationError(
                    error: ValidationErrors::typeMismatch,
                    description: '(expected: ' . $this->type . ' actual: ' . $resource->type . ')',
                    validatorType: ValidatorTypes::resource,
                )
            );
            return false;
        }

        if ($this->isIdRequired && $resource->id === null){
            $this->setValidationError(
                new ValidationError(
                    error: ValidationErrors::idMissing,
                    description: '',
                    validatorType: ValidatorTypes::resource,
                )
            );

            return false;
        }

        foreach ($this->attributesValidator ?? [] as $attributeValidator){
            if (!$attributeValidator->validate($resource)){
                $this->setValidationError($attributeValidator->getValidationError());
                return false;
            }
        }

        return true;
    }
}