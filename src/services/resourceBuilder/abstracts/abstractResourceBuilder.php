<?php
namespace carlonicora\minimalism\jsonapi\abstracts;

use carlonicora\minimalism\exceptions\serviceNotFoundException;
use carlonicora\minimalism\services\factories\servicesFactory;
use carlonicora\minimalism\jsonapi\resources\resourceObject;
use carlonicora\minimalism\jsonapi\resources\resourceRelationship;
use carlonicora\minimalism\services\encrypter\encrypter;
use carlonicora\minimalism\services\resourceBuilder\factories\serviceFactory;
use carlonicora\minimalism\services\resourceBuilder\interfaces\resourceBuilderInterface;
use carlonicora\minimalism\services\resourceBuilder\resourceBuilder;

abstract class abstractResourceBuilder implements resourceBuilderInterface {
    /** @var servicesFactory  */
    protected servicesFactory $services;

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
    protected array $toManyRelationFields = [];
    /** @var array */
    protected array $customFields = [];

    /** @var resourceObject */
    public resourceObject $resource;

    /** @var array  */
    protected array $data;

    /**
     * abstractBusinessObject constructor.
     * @param servicesFactory $services
     * @param array $data
     * @throws serviceNotFoundException
     */
    public function __construct(servicesFactory $services, array $data) {
        $this->services = $services;

        $this->data = $data;

        $resourceArray = [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'attributes' => $this->getAttributes(),
        ];
        $this->resource = new resourceObject($resourceArray);

        $this->resource->addMetas($this->buildMeta());
        $this->resource->addLinks($this->buildLinks());

        foreach ($this->oneToOneRelationFields as &$toOneResourceBuilderClass) {
            if (false === is_array($toOneResourceBuilderClass)) {
                $toOneResourceBuilderClass = ['id' => $toOneResourceBuilderClass . 'Id', 'class' => $toOneResourceBuilderClass];
            }
        }

        foreach ($this->toManyRelationFields as &$toManyResourceBuilderClass) {
            if (false === is_array($toManyResourceBuilderClass)) {
                $toManyResourceBuilderClass = ['id' => $toManyResourceBuilderClass . 'Id', 'class' => $toManyResourceBuilderClass];
            }
        }
    }

    /**
     * @return array
     */
    protected function buildLinks() : array {
        return [];
    }

    /**
     * @return array
     */
    protected function buildMeta() : array {
        return [];
    }

    /**
     * @return resourceObject
     * @throws serviceNotFoundException
     */
    public function buildResource(): resourceObject {
        $meta = $this->getMeta();
        if (false === empty($meta)) {
            $this->resource->addMetas($meta);
        }

        $relationships = $this->getRelationships();
        if (false === empty($relationships)) {
            $this->resource->addRelationshipList($relationships);
        }

        return $this->resource;
    }

    /**
     * @return string
     * @throws serviceNotFoundException
     */
    protected function getId(): string {
        if (in_array($this->idField, $this->hashEncodedFields, true)) {
            /** @var encrypter $encrypter */
            $encrypter = $this->services->service(\carlonicora\minimalism\services\encrypter\factories\serviceFactory::class);
            return  $encrypter->encryptId((int)$this->data[$this->idField]);
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
     * @throws serviceNotFoundException
     */
    protected function getAttributes(): ?array {
        /** @var encrypter $encrypter */
        $encrypter = $this->services->service(\carlonicora\minimalism\services\encrypter\factories\serviceFactory::class);

        $attributes = [];
        foreach ($this->hashEncodedFields as $hashEncodedField) {
            if (false === empty($this->data[$hashEncodedField]) && $this->idField !== $hashEncodedField) {
                $attributes[$hashEncodedField] = $encrypter->encryptId((int)$this->data[$hashEncodedField]);
            }
        }

        foreach ($this->simpleFields as $simpleField) {
            if (isset($this->data[$simpleField]) && $this->data[$simpleField] !== null && !array_key_exists($simpleField, $attributes)) {
                $attributes[$simpleField] = $this->data[$simpleField];
            } else {
                $attributes[$simpleField] = null;
            }
        }

        foreach ($this->customFields as $customField) {
            $attributes[$customField] = $this->$customField($this->data);
        }

        return $attributes ?? null;
    }

    /**
     * @return array
     * @throws serviceNotFoundException
     */
    protected function getRelationships(): ?array {
        /** @var resourceBuilder $resourceBuilder */
        $resourceBuilder = $this->services->service(serviceFactory::class);

        $relationships = [];
        foreach ($this->oneToOneRelationFields as $relationFieldName => $config) {
            if (false === empty($this->data[$relationFieldName])) {
                /** @var abstractResourceBuilder $relatedResourceBuilder */
                $relatedResourceBuilder = $resourceBuilder->create($config['class'], $this->data[$relationFieldName]);

                $relationship = new resourceRelationship($relatedResourceBuilder->resource);

                $relationshipMeta = $this->getRelationshipMeta($relationFieldName);
                if (false === empty($relationshipMeta)) {
                    $relationship->addMetas($relationshipMeta);
                }

                $relationships[$relationFieldName] []= $relationship;
            }
        }

        foreach ($this->toManyRelationFields as $relationFieldName => $config) {
            if (false === empty($this->data[$relationFieldName])) {
                /** @var abstractResourceBuilder $relatedResourceBuilder */
                foreach ($this->data[$relationFieldName] as $relatedData) {
                    $relatedResourceBuilder = $resourceBuilder->create($config['class'], $relatedData);

                    $relationship = new resourceRelationship($relatedResourceBuilder->resource);

                    $relationshipMeta = $this->getRelationshipMeta($relationFieldName);
                    if (false === empty($relationshipMeta)) {
                        $relationship->addMetas($relationshipMeta);
                    }

                    $relationships[$relationFieldName] []= $relationship;
                }
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