<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Services\DataValidator\Interfaces\DataValidatorInterface;
use CarloNicora\Minimalism\Interfaces\MinimalismObjectInterface;
use CarloNicora\Minimalism\Services\DataValidator\Objects\DocumentValidator;
use Exception;

abstract class AbstractDataValidator implements DataValidatorInterface, MinimalismObjectInterface
{
    /** @var Document|null  */
    private ?Document $document=null;

    /** @var array|null  */
    private ?array $data=null;

    /**
     * @var DocumentValidator
     */
    protected DocumentValidator $documentValidator;

    /**
     * @return Document
     */
    final public function getDocument(
    ): Document
    {
        return $this->document;
    }

    /**
     * @return array
     */
    final public function getData(
    ): array
    {
        return $this->data;
    }

    /**
     * @param array $payload
     * @throws Exception
     */
    final public function setDocument(
        array $payload,
    ): void
    {
        $this->document = new Document($payload);
    }

    /**
     * @return bool
     */
    final public function validate(
    ): bool
    {
        if ($this->document === null){
            return false;
        }

        return ($this->validateStructure() && $this->validateData());
    }

    /**
     * @return bool
     */
    final public function validateStructure(): bool
    {
        return $this->documentValidator->validate($this->document);
    }

    /**
     * @return bool
     */
    public function validateData(
    ): bool
    {
        return true;
    }
}