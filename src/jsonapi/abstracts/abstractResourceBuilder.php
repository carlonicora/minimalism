<?php
namespace carlonicora\minimalism\jsonapi\abstracts;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\factories\encrypterFactory;
use carlonicora\minimalism\interfaces\configurationsInterface;
use carlonicora\minimalism\jsonapi\factories\resourceBuilderFactory;
use carlonicora\minimalism\jsonapi\interfaces\resourceBuilderInterface;
use carlonicora\minimalism\jsonapi\resources\resourceObject;
use carlonicora\minimalism\jsonapi\resources\resourceRelationship;

abstract class abstractResourceBuilder implements resourceBuilderInterface {
    /** @var abstractConfigurations  */
    protected configurationsInterface $configurations;

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
    protected array $oneToManyRelationFields = [];
    /** @var array */
    protected array $customFields = [];

    /**
     * abstractBusinessObject constructor.
     * @param configurationsInterface $configurations
     */
    public function __construct(configurationsInterface $configurations) {
        $this->configurations = $configurations;

        foreach ($this->oneToOneRelationFields as &$relatedBobjClass) {
            if (false === is_array($relatedBobjClass)) {
                $relatedBobjClass = ['id' => $relatedBobjClass . 'Id', 'class' => $relatedBobjClass];
            }
        }
    }

    /**
     * @param array $data
     * @return string|null
     */
    abstract protected function getSelfLink(array $data): ?string;

    public function buildResource(array $data): resourceObject {
        // TODO resourceObject should get id, type, attributes paramters in constructor
        $dontUseArray = [
            'id' => $this->getId($data),
            'type' => $this->getType(),
            'attributes' => $this->getAttributes($data),
        ];
        $resource = new resourceObject($dontUseArray);

        $links = $this->getLinks($data);
        if (false === empty($links)) {
            $resource->addLinks($links);
        }

        $meta = $this->getMeta($data);
        if (false === empty($meta)) {
            $resource->addMetas($meta);
        }

        $relationships = $this->getRelationships($data);
        if (false === empty($relationships)) {
            $resource->addRelationshipList($relationships);
        }

        return $resource;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function getId(array $data): string
    {
        if (in_array($this->idField, $this->hashEncodedFields, true)) {
            return encrypterFactory::encrypter()->encryptId((int)$data[$this->idField]);
        }

        return $data[$this->idField];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return substr(strrchr(static::class, '\\'), 1);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getAttributes(array $data): ?array
    {
        $attributes = [];
        foreach ($this->hashEncodedFields as $hashEncodedField) {
            if (false === empty($data[$hashEncodedField]) && $this->idField !== $hashEncodedField) {
                $attributes[$hashEncodedField] = encrypterFactory::encrypter()->encryptId((int)$data[$hashEncodedField]);
            }
        }

        foreach ($this->simpleFields as $simpleField) {
            if (isset($data[$simpleField]) && $data[$simpleField] !== null && !array_key_exists($simpleField, $attributes)) {
                $attributes[$simpleField] = $data[$simpleField];
            }
        }

        foreach ($this->customFields as $customField) {
            $result[$customField] = $this->$customField($data);
        }

        return $attributes ?? null;
    }

    /**
     * @param array $data
     * @return array|null
     */
    protected function getLinks(array $data): ?array {
        return [['self' => $this->getSelfLink($data)]];
    }

    /**
     * @param string $relationFieldName
     * @param array $data
     * @return array|null
     */
    protected function getRelationshipLinks(string $relationFieldName, array $data): ?array {
        return null;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getRelationships(array $data): ?array
    {
        $relationships = [];
        foreach ($this->oneToOneRelationFields as $relationFieldName => $config) {
            if (false === empty($data[$relationFieldName])) {
                /** @var resourceBuilderInterface $relatedResourceBuilder */
                $relatedResourceBuilder = resourceBuilderFactory::resourceBuilder($config['class']);
                // TODO resourceObject should get id, type, attributes paramters in constructor
                $dontUseArray = [
                    'id' => $relatedResourceBuilder->getId($data[$relationFieldName]),
                    'type' => $relatedResourceBuilder->getType(),
                ];
                $relationship = new resourceRelationship($dontUseArray);
                $relationshipMeta = $this->getRelationshipMeta($relationFieldName, $data);
                if (false === empty($relationshipMeta)) {
                    $relationship->addMetas($relationshipMeta);
                }

                $relationshipLinks = $this->getRelationshipLinks($relationFieldName, $data);
                if (false === empty($relationshipLinks)) {
                    $relationship->addLinks($relationshipLinks);
                }

                $relationships []= $relationship;
            }
        }

        return $relationships ?? null;
    }

    /**
     * @param array $data
     * @return array|null
     */
    protected function getMeta(array $data): ?array {
        return null;
    }

    /**
     * @param string $relationFieldName
     * @param array $data
     * @return array|null
     */
    protected function getRelationshipMeta(string $relationFieldName, array $data): ?array {
        return null;
    }

}