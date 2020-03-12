<?php
namespace carlonicora\minimalism\dataObjects;

use carlonicora\minimalism\abstracts\abstractResponseObject;
use carlonicora\minimalism\interfaces\responseInterface;

class errorObject extends abstractResponseObject implements responseInterface {

    /** @var int|null  */
    public ?int $id=null;

    /** @var string  */
    public ?string $code=null;

    /** @var string  */
    public string $title;

    /** @var string|null  */
    public ?string $detail=null;

    /** @var array|null  */
    public ?array $meta=null;

    /**
     * errorObject constructor.
     * @param string $httpStatusCode
     * @param string|null $detail
     * @param string|null $code
     * @param int|null $id
     * @param array|null $meta
     */
    public function __construct(string $httpStatusCode, string $detail=null, string $code=null, int $id = null, array $meta=null) {
        $this->status = $httpStatusCode;
        $this->title = $this->generateText();
        $this->detail = $detail;
        $this->code = $code;
        $this->id = $id;
        $this->meta = $meta;
    }

    /**
     * @return string
     */
    public function toJson(): string {
        $response = [
            'status' => $this->status
        ];

        if ($this->id !== null){
            $response['id'] = $this->id;
        }

        if ($this->code !== null){
            $response['code'] = $this->code;
        }

        if ($this->title !== null){
            $response['title'] = $this->title;
        }

        if ($this->detail !== null){
            $response['detail'] = $this->detail;
        }

        if ($this->meta !== null){
            $response['meta'] = $this->meta;
        }

        return json_encode($response, JSON_THROW_ON_ERROR, 512);
    }
}