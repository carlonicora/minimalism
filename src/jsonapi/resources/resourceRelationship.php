<?php
namespace carlonicora\minimalism\jsonapi\resources;

class resourceRelationship {
    use resourceLinks;
    use resourceMeta;

    /** @var resourceObject  */
    public resourceObject $data;

    /**
     * resourceRelationship constructor.
     * @param array $data
     */
    public function __construct(array $data) {
        $this->data = new resourceObject($data);
    }

    /**
     * @param bool $includesAttributes
     * @return array
     */
    public function toArray(bool $includesAttributes=true) : array {
        $response = [
            'data' => $this->data->toArray($includesAttributes)
        ];

        if ($this->links !== null){
            $response['links'] = $this->linksToArray();
        }

        if ($this->meta) {
            $response['meta'] = $this->meta;
        }

        return $response;
    }
}