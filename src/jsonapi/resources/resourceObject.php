<?php
namespace carlonicora\minimalism\jsonapi\resources;

use carlonicora\minimalism\jsonapi\traits\linksTrait;

class resourceObject extends resourceIdentifierObject {
    use linksTrait;

    /** @var array|null */
    public ?array $attributes=null;

    /** @var array|null */
    public ?array $relationships=null;

    /**
     * resourceObject constructor.
     * @param array $data
     */
    public function __construct(array $data) {
        parent::__construct($data['type'], $data['id']);

        if (array_key_exists('attributes', $data)){
            $this->attributes = $data['attributes'];
        }
    }

    /**
     * @param resourceRelationship $relationship
     * @param string|null $relationshipName
     */
    public function addRelationship(resourceRelationship $relationship, string $relationshipName = null) : void{
        if ($this->relationships === null){
            $this->relationships = [];
        }

        if (null === $relationshipName) {
            $relationshipName = $relationship->data->type;
        }

        $this->relationships[$relationshipName][] = $relationship;
    }

    /**
     * @param array $relationships
     */
    public function addRelationshipList(array $relationships): void {
        /** @var resourceRelationship $relationship */
        foreach ($relationships ?? [] as $relationshipName => $relationshipList) {
            foreach ($relationshipList as $relationship) {
                $this->addRelationship($relationship, $relationshipName);
            }
        }
    }

    /**
     * @param bool $limitToIdentifierObject
     * @return array
     */
    public function toArray(bool $limitToIdentifierObject=false) : array {
        $response = parent::toArray();

        if (!$limitToIdentifierObject) {
            if ($this->attributes !== null) {
                $response['attributes'] = $this->attributes;
            }

            if ($this->hasLinks()) {
                $response['links'] = $this->links;
            }

            if ($this->relationships !== null) {
                $response['relationships'] = [];

                foreach ($this->relationships as $type => $relationships) {
                    $response['relationships'][$type]['data'] = [];

                    /** @var resourceRelationship $relationship */
                    foreach ($relationships ?? [] as $relationship) {
                        $response['relationships'][$type]['data'][] = $relationship->data->toArray(true);
                    }
                }
            }
        }

        return $response;
    }
}