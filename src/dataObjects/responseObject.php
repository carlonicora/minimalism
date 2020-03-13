<?php
namespace carlonicora\minimalism\dataObjects;

use carlonicora\minimalism\abstracts\abstractResponseObject;
use carlonicora\minimalism\interfaces\responseInterface;

class responseObject extends abstractResponseObject implements responseInterface {
    use resourceMeta;
    use resourceLinks;

    /** @var resourceObject|null */
    public ?resourceObject $data=null;

    /**
     * responseObject constructor.
     * @param array $data
     */
    public function __construct(array $data=null) {
        if (isset($data)){
            $this->data = new resourceObject($data);
        }
        $this->status = self::HTTP_STATUS_200;
    }

    /**
     * @return array
     */
    public function toArray(): array {
        $response = [
            'data' => $this->data->toArray()
        ];

        if (!empty($this->links)){
            $response['links'] = $this->linksToArray();
        }

        if (!empty($this->meta)){
            $response['meta'] = $this->meta;
        }

        return $response;
    }

    /**
     * @return string
     */
    public function toJson() : string {
        $response = $this->toArray();

        return json_encode($response, JSON_THROW_ON_ERROR, 512);
    }

}