<?php
namespace carlonicora\minimalism\jsonapi\responses;

use carlonicora\minimalism\abstracts\abstractResponseObject;
use carlonicora\minimalism\interfaces\responseInterface;
use carlonicora\minimalism\jsonapi\resources\errorObject;
use carlonicora\minimalism\jsonapi\traits\metaTrait;

class errorResponse extends abstractResponseObject implements responseInterface {
    use metaTrait;

    /** @var array  */
    public array $errors=[];

    /**
     * errorObject constructor.
     * @param errorObject $error
     */
    public function __construct(errorObject $error) {
        $this->errors[] = $error;

        $this->status = $error->status;
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