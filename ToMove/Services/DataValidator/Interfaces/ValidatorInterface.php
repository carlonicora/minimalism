<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Interfaces;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\ResourceObject;
use CarloNicora\Minimalism\Services\DataValidator\Objects\ValidationError;

interface ValidatorInterface
{
    /**
     * @return ValidationError|null
     */
    public function getValidationError(): ?ValidationError;

    /**
     * @param ValidationError $validationError
     */
    public function setValidationError(ValidationError $validationError): void;

    /**
     * @param Document|ResourceObject $resource
     * @return bool
     */
    public function validate(Document|ResourceObject $resource): bool;
}