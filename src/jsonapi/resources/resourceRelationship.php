<?php
namespace carlonicora\minimalism\jsonapi\resources;

use carlonicora\minimalism\jsonapi\traits\linksTrait;
use carlonicora\minimalism\jsonapi\traits\metaTrait;

class resourceRelationship {
    use linksTrait;
    use metaTrait;

    /** @var resourceObject  */
    public resourceObject $data;

    /**
     * resourceRelationship constructor.
     * @param resourceObject $resource
     */
    public function __construct(resourceObject $resource) {
        $this->data = $resource;
    }

    /**
     * @param bool $limitToIdentifierObject
     * @return array
     */
    public function toArray(bool $limitToIdentifierObject=false) : array {
        $response = [
            'data' => $this->data->toArray($limitToIdentifierObject)
        ];

        if (!empty($this->links)){
            $response['links'] = $this->links;
        }

        if (!empty($this->meta)) {
            $response['meta'] = $this->meta;
        }

        return $response;
    }
}