<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Objects;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\ResourceObject;
use CarloNicora\Minimalism\Services\DataValidator\Abstracts\AbstractValidator;
use CarloNicora\Minimalism\Services\DataValidator\Enums\ValidationErrors;
use CarloNicora\Minimalism\Services\DataValidator\Enums\ValidatorTypes;

class DocumentValidator extends AbstractValidator
{
    /** @var array  */
    private array $resourcesValidator=[];

    /**
     * @param bool $isSingleResource
     */
    public function __construct(
        public bool $isSingleResource=true,
    )
    {
    }

    /**
     * @param ResourceValidator $validator
     */
    public function addResourceValidator(
        ResourceValidator $validator,
    ): void
    {
        $this->resourcesValidator[] = $validator;
    }

    /**
     * @param Document|ResourceObject $resource
     * @return bool
     */
    public function validate(
        Document|ResourceObject $resource,
    ): bool
    {
        if ($this->isSingleResource && count($resource->resources) !== 1) {
            $this->setValidationError(
                new ValidationError(
                    error: ValidationErrors::numberOfResourcesMismatch,
                    description: 'actual ' . count($resource->resources) . 'expected 1',
                    validatorType: ValidatorTypes::document,
                )
            );

            return false;
        }

        foreach ($resource->resources ?? [] as $singleResource) {
            foreach ($this->resourcesValidator ?? [] as $resourceValidator) {
                if (!$resourceValidator->validate($singleResource)) {
                    $this->setValidationError($resourceValidator->getValidationError());
                    return false;
                }
            }
        }

        return true;
    }
}