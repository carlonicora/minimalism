<?php
namespace carlonicora\minimalism\jsonapi\responses;

use carlonicora\minimalism\abstracts\abstractResponseObject;
use carlonicora\minimalism\interfaces\responseInterface;
use carlonicora\minimalism\jsonapi\traits\metaTrait;

class errorResponse extends abstractResponseObject implements responseInterface {
    use metaTrait;

    /** @var int|null  */
    public ?int $id=null;

    /** @var string  */
    public ?string $code=null;

    /** @var string  */
    public string $title;

    /** @var string|null  */
    public ?string $detail=null;

    /**
     * errorObject constructor.
     * @param string $httpStatusCode
     * @param string|null $detail
     * @param string|null $code
     * @param int|null $id
     */
    public function __construct(string $httpStatusCode, string $detail=null, string $code=null, int $id = null) {
        $this->status = $httpStatusCode;
        $this->title = $this->generateText();
        $this->detail = $detail;
        $this->code = $code;
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function toArray(): array {
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

        if (!empty($this->meta)){
            $response['meta'] = $this->meta;
        }

        return $response;
    }

    /**
     * @return string
     */
    public function toJson(): string {
        $response = $this->toArray();

        return json_encode($response, JSON_THROW_ON_ERROR, 512);
    }
}