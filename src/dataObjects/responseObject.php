<?php
namespace carlonicora\minimalism\dataObjects;

use carlonicora\minimalism\abstracts\abstractResponseObject;
use carlonicora\minimalism\interfaces\responseInterface;

class responseObject extends abstractResponseObject implements responseInterface {
    /** @var array  */
    public array $data=[];

    /** @var array  */
    private array $meta=[];

    /** @var array  */
    private array $links=[];

    /**
     * responseObject constructor.
     * @param array $data
     * @param array|null $meta
     * @param string $httpStatusCode
     */
    public function __construct(array $data = [], array $meta=[], string $httpStatusCode = self::HTTP_STATUS_200) {
        $this->data = $data;
        $this->meta = $meta;
        $this->status = $httpStatusCode;
        $this->links = [];
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
        $this->meta[$name] = $value;
    }

    /**
     * @param string $name
     * @param string $url
     * @param array|null $meta
     */
    public function addLink(string $name, string $url, array $meta=null): void {
        if ($meta === null){
            $this->links[$name] = $url;
        } else {
            $this->links[$name] = [
                'href' => $url,
                'meta' => $meta
            ];
        }
    }

    /**
     * @return array
     */
    public function toArray(): array {
        $response = [
            'data' => $this->data
        ];

        if (!empty($this->links)){
            $response['links'] = $this->links;
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