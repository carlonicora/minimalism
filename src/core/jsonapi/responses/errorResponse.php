<?php
namespace carlonicora\minimalism\core\jsonapi\responses;

use carlonicora\minimalism\core\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\core\jsonapi\abstracts\abstractResponseObject;
use carlonicora\minimalism\core\jsonapi\resources\errorObject;
use carlonicora\minimalism\core\jsonapi\traits\metaTrait;

class errorResponse extends abstractResponseObject implements responseInterface {
    use metaTrait;

    /** @var array  */
    public array $errors=[];

    /**
     * errorObject constructor.
     * @param string $httpStatusCode
     * @param string|null $detail
     * @param string|null $code
     * @param int|null $id
     */
    public function __construct(string $httpStatusCode, string $detail=null, string $code=null, int $id = null) {
        $this->errors[] = new errorObject([
            'status' => $httpStatusCode,
            'detail' => $detail,
            'code' => $code,
            'id' => $id
        ]);

        $this->status = $httpStatusCode;
    }

    /**
     * @return array
     */
    public function toArray(): array {
        $response = [
            'errors' => []
        ];

        /** @var errorObject $error */
        foreach ($this->errors ?? [] as $error) {
            $response['errors'][] = $error->toArray();
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