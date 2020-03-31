<?php
namespace carlonicora\minimalism\jsonapi\resources;

use carlonicora\minimalism\jsonapi\abstracts\abstractResponseObject;
use carlonicora\minimalism\jsonapi\traits\metaTrait;

class errorObject extends abstractResponseObject {
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
     * @param array $error
     */
    public function __construct(array $error) {
        $this->status = $error['status'];
        $this->detail = $error['detail'];
        $this->code = $error['code'];
        $this->id = $error['id'];
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
     * @inheritDoc
     */
    public function toJson(): string {
        $response = $this->toArray();

        return json_encode($response, JSON_THROW_ON_ERROR, 512);
    }
}