<?php
namespace carlonicora\minimalism\jsonapi\responses;

use carlonicora\minimalism\abstracts\abstractResponseObject;
use carlonicora\minimalism\interfaces\responseInterface;
use carlonicora\minimalism\jsonapi\resources\resourceObject;
use carlonicora\minimalism\jsonapi\resources\resourceRelationship;
use carlonicora\minimalism\jsonapi\traits\linksTrait;
use carlonicora\minimalism\jsonapi\traits\metaTrait;

class dataResponse extends abstractResponseObject implements responseInterface {
    use metaTrait;
    use linksTrait;

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
     * @return array
     */
    public function toArray(): array {
        $response = [];

        if ($this->data !== null) {
            $response['data'] = $this->data->toArray();
            $this->buildIncluded($this->data);

        } else {
            $response['data'] = [];

            /** @var resourceObject $data */
            foreach ($this->dataList ?? [] as $data){
                $response['data'][] = $data->toArray();
                $this->buildIncluded($this->data);
            }
        }

        if (!empty($this->links)){
            $response['links'] = $this->links;
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
     * @param resourceObject $data
     */
    private function buildIncluded(resourceObject $data): void {
        if ($data->relationships !== null){
            foreach ($data->relationships as $relationshipType=>$relationships){
                /** @var resourceRelationship $relationship */
                foreach ($relationships as $relationship){
                    if ($this->included === null){
                        $this->included = [];
                    }

                    if (!in_array($relationship->data->id, array_column($this->included, 'id'), true)){
                        $this->included[] = $relationship;
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    public function toJson() : string {
        $response = $this->toArray();

        return json_encode($response, JSON_THROW_ON_ERROR, 512);
    }

}