<?php
namespace carlonicora\minimalism\jsonapi\abstracts;

use carlonicora\minimalism\factories\encrypterFactory;
use carlonicora\minimalism\interfaces\configurationsInterface;
use carlonicora\minimalism\jsonapi\factories\resourceBuilderFactory;
use carlonicora\minimalism\jsonapi\interfaces\resourceBuilderInterface;
use carlonicora\minimalism\jsonapi\resources\resourceObject;
use carlonicora\minimalism\jsonapi\resources\resourceRelationship;

abstract class abstractResourceBuilder implements resourceBuilderInterface {
    /** @var configurationsInterface  */
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

    /** @var resourceObject */
    public resourceObject $resource;

    /** @var array  */
    private array $data;

    /**
     * abstractBusinessObject constructor.
     * @param configurationsInterface $configurations
     * @param array $data
     */
    public function __construct(configurationsInterface $configurations, array $data) {
        $this->configurations = $configurations;

        $this->data = $data;

        $resourceArray = [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'attributes' => $this->getAttributes(),
        ];
        $this->resource = new resourceObject($resourceArray);

        $this->buildMeta();
        $this->buildLinks();

        foreach ($this->oneToOneRelationFields as &$relatedBobjClass) {
            if (false === is_array($relatedBobjClass)) {
                $relatedBobjClass = ['id' => $relatedBobjClass . 'Id', 'class' => $relatedBobjClass];
            }
        }
    }

    /**
     *
     */
    protected function buildLinks() : void {
    }

    /**
     *
     */
    protected function buildMeta() : void {
    }

    /**
     * @return resourceObject
     */
    public function buildResource(): resourceObject {
        $meta = $this->getMeta();
        if (false === empty($meta)) {
            $this->resource->addMetas($meta);
        }

        $relationships = $this->getRelationships($this->data);
        if (false === empty($relationships)) {
            $this->resource->addRelationshipList($relationships);
        }

        return $this->resource;
    }

    /**
     * @return string
     */
    protected function getId(): string {
        if (in_array($this->idField, $this->hashEncodedFields, true)) {
            return encrypterFactory::encrypter()->encryptId((int)$this->data[$this->idField]);
        }

        return $this->data[$this->idField];
    }

    /**
     * @return string
     */
    protected function getType(): string {
        return substr(strrchr(static::class, '\\'), 1, -2);
    }

    /**
     * @return array
     */
    protected function getAttributes(): ?array {
        $attributes = [];
        foreach ($this->hashEncodedFields as $hashEncodedField) {
            if (false === empty($this->data[$hashEncodedField]) && $this->idField !== $hashEncodedField) {
                $attributes[$hashEncodedField] = encrypterFactory::encrypter()->encryptId((int)$this->data[$hashEncodedField]);
            }
        }

        foreach ($this->simpleFields as $simpleField) {
            $attributes[$simpleField] = null;

            if (isset($this->data[$simpleField]) && $this->data[$simpleField] !== null && !array_key_exists($simpleField, $attributes)) {
                $attributes[$simpleField] = $this->data[$simpleField];
            }
        }

        foreach ($this->customFields as $customField) {
            $attributes[$customField] = $this->$customField($this->data);
        }

        return $attributes ?? null;
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
                /** @var abstractResourceBuilder $relatedResourceBuilder */
                $relatedResourceBuilder = resourceBuilderFactory::resourceBuilder($config['class'], $data);

                $relationship = new resourceRelationship($relatedResourceBuilder->resource);

                $relationshipMeta = $this->getRelationshipMeta($relationFieldName);
                if (false === empty($relationshipMeta)) {
                    $relationship->addMetas($relationshipMeta);
                }

                $relationships []= $relationship;
            }
        }

        return $relationships ?? null;
    }

    /**
     * @return array|null
     */
    protected function getMeta(): ?array {
        return null;
    }

    /**
     * @param string $relationFieldName
     * @return array|null
     * @noinspection PhpUnusedParameterInspection
     */
    protected function getRelationshipMeta(string $relationFieldName): ?array {
        return null;
    }
}