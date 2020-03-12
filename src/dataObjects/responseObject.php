<?php
namespace carlonicora\minimalism\dataObjects;

use carlonicora\minimalism\abstracts\abstractResponseObject;
use carlonicora\minimalism\interfaces\responseInterface;

class responseObject extends abstractResponseObject implements responseInterface {
    /** @var array  */
    public array $data=[];

    /** @var array|null  */
    private ?array $meta=null;

    /**
     * responseObject constructor.
     * @param array $data
     * @param array|null $meta
     * @param string $httpStatusCode
     */
    public function __construct(array $data = [], array $meta = null, string $httpStatusCode = self::HTTP_STATUS_200) {
        $this->data = $data;
        $this->meta = $meta;
        $this->status = $httpStatusCode;
    }

    /**
     * @param array $meta
     */
    public function setMeta(array $meta): void {
        $this->meta = $meta;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addMetaString(string $name, string $value): void {
        if ($this->meta === null){
            $this->meta = [];
        }

        $this->meta[$name] = $value;
    }

    /**
     * @return array
     */
    public function toArray(): array {
        $response = [
            'data' => $this->data
        ];

        if ($this->meta !== null){
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