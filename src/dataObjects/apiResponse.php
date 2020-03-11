<?php
namespace carlonicora\minimalism\dataObjects;

class apiResponse {
    /** @var string  */
    public const HTTP_STATUS_200='200';

    /** @var string  */
    public const HTTP_STATUS_405='405';

    /** @var array  */
    public array $data=[];

    /** @var array|null  */
    private ?array $meta=null;

    /** @var errorObject|null */
    private ?errorObject $error=null;

    /**
     * @param errorObject $error
     */
    public function addError(errorObject $error) : void {
        $this->error = $error;
    }

    /**
     * @param array $meta
     */
    public function addMeta(array $meta): void {
        $this->meta = $meta;
    }

    /**
     * @return string
     */
    public function generateHttpCode() : string {
        if ($this->error !== null){
            return $this->error->code;
        }

        return '200';
    }

    /**
     * @param string $code
     * @return string
     */
    public static function generateText(string $code) : string {
        switch ($code) {
            case self::HTTP_STATUS_405:
                return 'Method Not Allowed';
                break;
            case self::HTTP_STATUS_200:
            default:
                return 'OK';
                break;
        }

    }

    /**
     * @return string
     */
    public static function generateProtocol() : string {
        return ($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1');
    }

    /**
     * @return string
     */
    public function toJson() : string {
        $response = [];

        if ($this->error !== null){
            $response['error'] = $this->error->generatePublicArray();
        } else {
            $response['data'] = $this->data;
        }

        if ($this->meta !== null){
            $response['meta'] = $this->meta;
        }

        return json_encode($response, JSON_THROW_ON_ERROR, 512);
    }
}