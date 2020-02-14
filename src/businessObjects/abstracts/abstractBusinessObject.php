<?php
namespace carlonicora\minimalism\businessObjects\abstracts;

use carlonicora\minimalism\businessObjects\interfaces\businessObjectsInterface;
use carlonicora\minimalism\helpers\idEncrypter;

abstract class abstractBusinessObject implements businessObjectsInterface {

    /** @var string */
    public $idField;
    /** @var string */
    public $parentId;

    /** @var array */
    protected $hashEncodedFields = [];
    /** @var array */
    protected $simpleFields = [];
    /** @var array */
    protected $oneToOneRelationFields = [];

    /** @var idEncrypter */
    protected $encrypter;

    public function __construct(idEncrypter $encrypter) {
        $this->encrypter = $encrypter;
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
            $result[$simpleField] = $data[$simpleField];
        }

        foreach ($this->oneToOneRelationFields as $relationFieldName => $relatedBobjClass) {
            /** @var businessObjectsInterface $relatedBusinessObject */
            $relatedBusinessObject = new $relatedBobjClass($this->encrypter);

            // TODO separate related object data to $data[$relationFieldName]
            $result[$relationFieldName] = $relatedBusinessObject->fromDbModel($data);
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
            $result[$simpleField] = $data[$simpleField];
        }

        foreach ($this->oneToOneRelationFields as $relationFieldName => $relatedBobjClass) {
            /** @var self $relatedBusinessObject */
            $relatedBusinessObject = $data[$relationFieldName];
            $result[$relationFieldName . 'Id'] = $relatedBusinessObject[$relatedBusinessObject->idField];
        }

        return $result;
    }

}