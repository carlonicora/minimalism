<?php
namespace carlonicora\minimalism\core\jsonapi\responses;

use carlonicora\minimalism\core\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\core\jsonapi\abstracts\abstractResponseObject;
use carlonicora\minimalism\core\jsonapi\resources\errorObject;
use carlonicora\minimalism\core\jsonapi\resources\resourceObject;
use carlonicora\minimalism\core\jsonapi\resources\resourceRelationship;
use carlonicora\minimalism\core\jsonapi\traits\linksTrait;
use carlonicora\minimalism\core\jsonapi\traits\metaTrait;

class dataResponse extends abstractResponseObject implements responseInterface {
    use metaTrait;
    use linksTrait;

    /** @var resourceObject|null */
    public ?resourceObject $data=null;

    /** @var array|null  */
    public ?array $errors=null;

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
    }

    /**
     * @param errorObject $error
     */
    public function addError(errorObject $error) : void {
        if ($this->errors === null){
            $this->errors = [];
        }

        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    public function toArray(): array {
        $response = [];

        if ($this->data !== null) {
            $response['data'] = $this->data->toArray();
            $this->buildIncluded($this->data);
        } else if ($this->dataList !== null) {
            $response['data'] = [];

            /** @var resourceObject $data */
            foreach ($this->dataList ?? [] as $data){
                $response['data'][] = $data->toArray();
                $this->buildIncluded($data);
            }
        } else {
            $response['errors'] = [];
            /** @var errorObject $error */
            foreach ($this->errors ?? [] as $error) {
                $response['errors'][] = $error->toArray();

                if ($this->status === self::HTTP_STATUS_200){
                    $this->status = $error->status;
                }
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
    public function addIncluded(resourceObject $data): void {
        if ($this->included === null){
            $this->included = [];
        }

        $this->included[] = $data;
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
                        $this->included[] = $relationship->data;
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