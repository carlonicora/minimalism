<?php
namespace carlonicora\minimalism\businessObjects\abstracts;

use carlonicora\minimalism\businessObjects\interfaces\businessObjectsInterface;
use carlonicora\minimalism\factories\encrypterFactory;
use carlonicora\minimalism\helpers\idEncrypter;

abstract class abstractBusinessObject implements businessObjectsInterface {

    /** @var string */
    public string $idField;
    /** @var string */
    public string $parentId;

    /** @var array */
    protected array $hashEncodedFields = [];
    /** @var array */
    protected array $simpleFields = [];
    /** @var array */
    protected array $oneToOneRelationFields = [];

    /** @var idEncrypter */
    protected idEncrypter $encrypter;

    public function __construct() {
        $this->encrypter = encrypterFactory::encrypter();
    }

    /**
     * @inheritDoc
     */
    public function fromDbModel(array $data): array {
        $result = [];

        foreach ($this->hashEncodedFields as $hashEncodedField) {
            if (false === empty($data[$hashEncodedField])) {
                $result[$hashEncodedField] = $this->encrypter->encryptId((int)$data[$hashEncodedField]);
            }
        }

        foreach ($this->simpleFields as $simpleField) {
            if (isset($data[$simpleField]) && $data[$simpleField] !== null) {
                $result[$simpleField] = $data[$simpleField];
            }
        }

        foreach ($this->oneToOneRelationFields as $relationFieldName => $relatedBobjClass) {
            if (false === empty($data[$relationFieldName])) {
                /** @var abstractBusinessObject $relatedBusinessObject */
                $relatedBusinessObject = new $relatedBobjClass();
                $result[$relationFieldName] = $relatedBusinessObject->fromDbModel($data[$relationFieldName]);
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function toDbModel(array $data): array {
        $result = [];

        foreach ($this->hashEncodedFields as $hashEncodedField) {
            if (false === empty($data[$hashEncodedField])) {
                $result[$hashEncodedField] = $this->encrypter->decryptId($data[$hashEncodedField]);
            }
        }

        foreach ($this->simpleFields as $simpleField) {
            $result[$simpleField] = $data[$simpleField] ?? null;
        }

        foreach ($this->oneToOneRelationFields as $relationFieldName => $relatedBobjClass) {
            if (false === empty($data[$relationFieldName])) {
                /** @var self $relatedBusinessObject */
                $relatedBusinessObject = $data[$relationFieldName];
                $result[$relationFieldName . 'Id'] = $relatedBusinessObject[$relatedBusinessObject->idField];
            }
        }

        return $result;
    }

}