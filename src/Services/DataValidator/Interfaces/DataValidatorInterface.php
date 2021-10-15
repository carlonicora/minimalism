<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Interfaces;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Services\DataValidator\Objects\ValidationError;

interface DataValidatorInterface
{
    /**
     * @param array $payload
     */
    public function setDocument(array $payload): void;

    /**
     * @return Document
     */
    public function getDocument(): Document;

    /**
     * @return ValidationError|null
     */
    public function getValidationError(): ?ValidationError;

    /**
     * @return bool
     */
    public function validate(): bool;

    /**
     * @return bool
     */
    public function validateStructure(): bool;

    /**
     * @return bool
     */
    public function validateData(): bool;

    /**
     * @return array
     */
    public function getData(): array;
}