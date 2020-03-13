<?php
namespace carlonicora\minimalism\businessObjects\abstracts;

use carlonicora\minimalism\businessObjects\interfaces\businessObjectsInterface;
use carlonicora\minimalism\businessObjects\minimalismBo;
use carlonicora\minimalism\factories\encrypterFactory;

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
    /** @var array */
    protected array $customFields = [];

    public function __construct() {
        foreach ($this->oneToOneRelationFields as &$relatedBobjClass) {
            if (false === is_array($relatedBobjClass)) {
                $relatedBobjClass = ['id' => $relatedBobjClass . 'Id', 'class' => $relatedBobjClass];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function fromDbModel(array $data): array {
        $result = [
            'type' => substr(strrchr(static::class, '\\'), 1),
            'attributes' => []
        ];

        if (false === empty($data[$this->idField])) {
            if (array_key_exists($this->idField, $this->hashEncodedFields)){
                $result['id'] = encrypterFactory::encrypter()->encryptId((int)$data[$this->idField]);
            } else {
                $result['id'] = (int)$data[$this->idField];
            }
        }

        foreach ($this->hashEncodedFields as $hashEncodedField) {
            if (false === empty($data[$hashEncodedField])) {
                $result['attributes'][$hashEncodedField] = encrypterFactory::encrypter()->encryptId((int)$data[$hashEncodedField]);
            }
        }

        foreach ($this->simpleFields as $simpleField) {
            if (isset($data[$simpleField]) && $data[$simpleField] !== null) {
                $result['attributes'][$simpleField] = $data[$simpleField];
            }
        }

        foreach ($this->oneToOneRelationFields as $relationFieldName => $config) {
            if (false === empty($data[$relationFieldName])) {
                /** @var abstractBusinessObject $relatedBusinessObject */
                $relatedBusinessObject = new $config['class']();
                $result['attributes'][$relationFieldName] = $relatedBusinessObject->fromDbModel($data[$relationFieldName]);
            }
        }

        foreach ($this->customFields as $customField) {
            $method = $customField . 'FromDb';
            $result['attributes'][$customField] = $this->$method($data[$customField] ?? null);
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
                $result[$hashEncodedField] = encrypterFactory::encrypter()->decryptId($data[$hashEncodedField]);
            }
        }

        foreach ($this->simpleFields as $simpleField) {
            $result[$simpleField] = $data[$simpleField] ?? null;
        }

        foreach ($this->oneToOneRelationFields as $relationFieldName => $config) {
            if (false === empty($data[$relationFieldName])) {
                /** @var self $relatedBusinessObject */
                $relatedBusinessObject = $data[$relationFieldName];
                $result[$config['id']] = $relatedBusinessObject[$relatedBusinessObject->idField];
            }
        }

        foreach ($this->customFields as $customField) {
            $method = $customField . 'ToDb';
            $result[$customField] = $this->$method($data[$customField] ?? null);
        }

        return $result;
    }

}