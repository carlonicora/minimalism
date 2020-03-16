<?php
namespace carlonicora\minimalism\jsonapi\resources;

class resourceObject extends resourceIdentifierObject {
    use hasLinks;

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
     */
    public function addRelationship(resourceRelationship $relationship) : void{
        if ($this->relationships === null){
            $this->relationships = [];
        }

        if (!array_key_exists($relationship->data->type, $this->relationships)){
            $this->relationships[$relationship->data->type] = [];
        }

        $this->relationships[$relationship->data->type][] = $relationship;
    }

    /**
     * @param bool $includesAttributes
     * @return array
     */
    public function toArray(bool $includesAttributes=true) : array {
        $response = parent::toArray();

        if ($includesAttributes && $this->attributes !== null) {
            $response['attributes'] = $this->attributes;
        }

        if ($this->hasLinks()){
            $response['links'] = $this->links;
        }

        if ($this->relationships !== null){
            $response['relationships'] = [];

            foreach ($this->relationships as $type => $relationships){
                $response['relationships'][$type]['data'] = [];

                /** @var resourceRelationship $relationship */
                foreach ($relationships ?? [] as $relationship){
                    $response['relationships'][$type]['data'][] = $relationship->data->toArray(false);
                }
            }
        }

        return $response;
    }
}