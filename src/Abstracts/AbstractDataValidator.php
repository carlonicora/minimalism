<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Interfaces\DataValidatorInterface;
use CarloNicora\Minimalism\Interfaces\MinimalismObjectInterface;
use Exception;

abstract class AbstractDataValidator implements DataValidatorInterface, MinimalismObjectInterface
{
    /** @var Document|null  */
    private ?Document $document=null;

    /** @var array|null  */
    private ?array $data=null;

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
     * @param array $payload
     * @throws Exception
     */
    public function setDocument(
        array $payload,
    ): void
    {
        $this->document = new Document($payload);
    }

    /**
     * @return Document
     */
    public function getDocument(
    ): Document
    {
        return $this->document;
    }

    /**
     * @return bool
     */
    public function validateData(
    ): bool
    {
        return true;
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
     * @return bool
     */
    abstract public function validateStructure(): bool;
}