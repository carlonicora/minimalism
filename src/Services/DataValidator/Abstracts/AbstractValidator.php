<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\ResourceObject;
use CarloNicora\Minimalism\Services\DataValidator\Interfaces\ValidatorInterface;
use CarloNicora\Minimalism\Services\DataValidator\Objects\ValidationError;

abstract class AbstractValidator implements ValidatorInterface
{
    /** @var string|null  */
    protected ?string $name=null;

    /** @var ValidationError|null  */
    private ?ValidationError $validationError;

    /**
     * @return ValidationError|null
     */
    final public function getValidationError(
    ): ?ValidationError
    {
        return $this->validationError;
    }

    /**
     * @param ValidationError $validationError
     */
    final public function setValidationError(
        ValidationError $validationError,
    ): void
    {
        $this->validationError = $validationError;
    }

    /**
     * @param Document|ResourceObject $resource
     * @return bool
     */
    abstract public function validate(Document|ResourceObject $resource): bool;
}