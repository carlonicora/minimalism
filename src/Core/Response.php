<?php
namespace CarloNicora\Minimalism\Core;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    /** @var string  */
    private string $data='';

    /** @var string  */
    private string $contentType='text/html';

    /** @var string  */
    private string $httpStatus=self::HTTP_STATUS_200;

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @param string $httpContentType
     */
    public function setContentType(string $httpContentType): void
    {
        $this->contentType = $httpContentType;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->httpStatus;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->httpStatus = $status;
    }

    /**
     *
     */
    public function write() : void
    {
        $this->writeProtocol();
        $this->writeContentType();

        if ($this->httpStatus !== self::HTTP_STATUS_201
            && $this->httpStatus !== self::HTTP_STATUS_204
            && $this->httpStatus !== self::HTTP_STATUS_304) {
            echo $this->getData();
        }
    }

    /**
     *
     */
    public function writeContentType() : void
    {
        header('Content-Type: ' . $this->getContentType());
    }

    /**
     *
     */
    public function writeProtocol() : void
    {
        http_response_code((int)$this->getStatus());
        $GLOBALS['http_response_code'] = $this->getStatus();
        header($this->getProtocol() . ' ' . $this->getStatus() . ' ' . $this->generateStatusText());
    }

    /**
     * @return string
     */
    private function getProtocol() : string
    {
        return ($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1');
    }

    /**
     * @return string
     */
    private function generateStatusText() : string
    {
        switch ($this->getStatus()) {
            case self::HTTP_STATUS_201:
                return 'Created';
                break;
            case self::HTTP_STATUS_204:
                return 'No Content';
                break;
            case self::HTTP_STATUS_304:
                return 'Not Modified';
                break;
            case self::HTTP_STATUS_400:
                return 'Bad Request';
                break;
            case self::HTTP_STATUS_401:
                return 'Unauthorized';
                break;
            case self::HTTP_STATUS_403:
                return 'Forbidden';
                break;
            case self::HTTP_STATUS_404:
                return 'Not Found';
                break;
            case self::HTTP_STATUS_405:
                return 'Method Not Allowed';
                break;
            case self::HTTP_STATUS_406:
                return 'Not Acceptable';
                break;
            case self::HTTP_STATUS_409:
                return 'Conflict';
                break;
            case self::HTTP_STATUS_410:
                return 'Gone';
                break;
            case self::HTTP_STATUS_411:
                return 'Length Required';
                break;
            case self::HTTP_STATUS_412:
                return 'Precondition Failed';
                break;
            case self::HTTP_STATUS_415:
                return 'Unsupported Media Type';
                break;
            case self::HTTP_STATUS_422:
                return 'Unprocessable Entity';
                break;
            case self::HTTP_STATUS_428:
                return 'Precondition Required';
                break;
            case self::HTTP_STATUS_429:
                return 'Too Many Requests';
                break;
            case self::HTTP_STATUS_500:
                return 'Internal Server Error';
                break;
            case self::HTTP_STATUS_501:
                return 'Not Implemented';
                break;
            case self::HTTP_STATUS_502:
                return 'Bad Gateway';
                break;
            case self::HTTP_STATUS_503:
                return 'Service Unavailable';
                break;
            case self::HTTP_STATUS_504:
                return 'Gateway Timeout';
                break;
            case self::HTTP_STATUS_200:
            default:
                return 'OK';
                break;
        }
    }
}