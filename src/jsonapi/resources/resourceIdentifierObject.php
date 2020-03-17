<?php
namespace carlonicora\minimalism\jsonapi\resources;

use carlonicora\minimalism\jsonapi\traits\metaTrait;

class resourceIdentifierObject {
    use metaTrait;

    /** @var string  */
    public string $type;

    /** @var string */
    public string $id;

    /**
     * resourceIdentifierObject constructor.
     * @param string $type
     * @param string $id
     */
    public function __construct(string $type, string $id) {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function toArray() : array{
        $response = [
            'type' => $this->type,
            'id' => $this->id
        ];

        if (!empty($this->meta)){
            $response['meta'] = $this->meta;
        }

        return $response;
    }
}