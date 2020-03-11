<?php
namespace carlonicora\minimalism\dataObjects;

class errorObject {
    /** @var int|null  */
    public ?int $id=null;

    /** @var string|null  */
    public ?string $status=null;

    /** @var string  */
    public string $code;

    /** @var string  */
    public string $title;

    /** @var string|null  */
    public ?string $detail=null;

    /** @var array|null  */
    public ?array $meta=null;

    /**
     * errorObject constructor.
     * @param string $code
     * @param string|null $detail
     */
    public function __construct(string $code, string $detail=null) {
        $this->code = $code;

        $this->title = apiResponse::generateText($code);

        if ($detail !== null){
            $this->detail = $detail;
        }
    }

    /**
     * @return array
     */
    public function generatePublicArray(): array {
        $response = [
            'code' => $this->code
        ];

        if ($this->id !== null){
            $response['id'] = $this->id;
        }

        if ($this->status !== null){
            $response['status'] = $this->status;
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

        return $response;
    }
}