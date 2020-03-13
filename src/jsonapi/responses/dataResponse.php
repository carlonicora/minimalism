<?php
namespace carlonicora\minimalism\jsonapi\responses;

use carlonicora\minimalism\abstracts\abstractResponseObject;
use carlonicora\minimalism\interfaces\responseInterface;
use carlonicora\minimalism\jsonapi\resources\resourceLinks;
use carlonicora\minimalism\jsonapi\resources\resourceMeta;
use carlonicora\minimalism\jsonapi\resources\resourceObject;
use carlonicora\minimalism\jsonapi\resources\resourceRelationship;

class dataResponse extends abstractResponseObject implements responseInterface {
    use resourceMeta;
    use resourceLinks;

    /** @var resourceObject|null */
    public ?resourceObject $data=null;

    /** @var array|null */
    public ?array $dataList=null;

    /** @var array|null  */
    public ?array $included=null;

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
     * @param resourceRelationship $relationship
     */
    public function addIncluded(resourceRelationship $relationship) : void{
        if ($this->included === null){
            $this->included = [];
        }

        $this->included[] = $relationship;
    }

    /**
     * @return array
     */
    public function toArray(): array {
        $response = [];

        if ($this->data !== null) {
            $response['data'] = $this->data->toArray();
        } else {
            $response['data'] = [];

            /** @var resourceObject $data */
            foreach ($this->dataList ?? [] as $data){
                $dataObject = $data->toArray();
                $response['data'][] = $dataObject['data'];
            }
        }

        if (!empty($this->links)){
            $response['links'] = $this->linksToArray();
        }

        if (!empty($this->meta)){
            $response['meta'] = $this->meta;
        }

        if ($this->included !== null){
            $response['included'] = [];

            /** @var resourceRelationship $resource */
            foreach ($this->included as $resource){
                $response['included'][] = $resource->toArray();
            }
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