<?php
namespace carlonicora\minimalism\dataObjects;

class resourceObject extends resourceIdentifierObject {
    use resourceLinks;

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
     * @return array
     */
    public function toArray() : array {
        $response = parent::toArray();

        if ($this->attributes !== null){
            $response['attributes'] = $this->attributes;
        }

        if ($this->hasLinks()){
            $response['links'] = $this->linksToArray();
        }

        return $response;
    }
}