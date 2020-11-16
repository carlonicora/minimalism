<?php
namespace CarloNicora\Minimalism\Core;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    /** @var bool  */
    private bool $isHttpRequest=true;

    /** @var string  */
    private string $data='';

    /** @var string  */
    private string $contentType='text/html';

    /** @var string  */
    private string $httpStatus=self::HTTP_STATUS_200;

    /** @var string|null  */
    private ?string $redirection=null;

    /** @var array  */
    private array $redirectionParameters=[];

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
     * @return mixed|void
     */
    public function setNotHttpResponse()
    {
        $this->isHttpRequest = false;
    }

    /**
     *
     */
    public function write() : void
    {
        if ($this->isHttpRequest) {
            $this->writeProtocol();
            $this->writeContentType();

            if ($this->httpStatus !== self::HTTP_STATUS_204
                && $this->httpStatus !== self::HTTP_STATUS_304) {
                echo $this->getData();
            }
        } else {
            echo $this->getData();
        }
    }

    /**
     * Allows for a way to unit test the header calls within this class
     *
     * @param $string
     */
    protected function writeRawHTTP($string): void
    {
        header($string);
    }

    /**
     *
     */
    public function writeContentType() : void
    {
        $this->writeRawHTTP('Content-Type: ' . $this->getContentType());
    }

    /**
     *
     */
    public function writeProtocol() : void
    {
        http_response_code((int)$this->getStatus());
        $GLOBALS['http_response_code'] = $this->getStatus();
        $this->writeRawHTTP($this->getProtocol() . ' ' . $this->getStatus() . ' ' . $this->generateStatusText());
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
            case self::HTTP_STATUS_204:
                return 'No Content';
            case self::HTTP_STATUS_304:
                return 'Not Modified';
            case self::HTTP_STATUS_400:
                return 'Bad Request';
            case self::HTTP_STATUS_401:
                return 'Unauthorized';
            case self::HTTP_STATUS_403:
                return 'Forbidden';
            case self::HTTP_STATUS_404:
                return 'Not Found';
            case self::HTTP_STATUS_405:
                return 'Method Not Allowed';
            case self::HTTP_STATUS_406:
                return 'Not Acceptable';
            case self::HTTP_STATUS_409:
                return 'Conflict';
            case self::HTTP_STATUS_410:
                return 'Gone';
            case self::HTTP_STATUS_411:
                return 'Length Required';
            case self::HTTP_STATUS_412:
                return 'Precondition Failed';
            case self::HTTP_STATUS_415:
                return 'Unsupported Media Type';
            case self::HTTP_STATUS_422:
                return 'Unprocessable Entity';
            case self::HTTP_STATUS_428:
                return 'Precondition Required';
            case self::HTTP_STATUS_429:
                return 'Too Many Requests';
            case self::HTTP_STATUS_500:
                return 'Internal Server Error';
            case self::HTTP_STATUS_501:
                return 'Not Implemented';
            case self::HTTP_STATUS_502:
                return 'Bad Gateway';
            case self::HTTP_STATUS_503:
                return 'Service Unavailable';
            case self::HTTP_STATUS_504:
                return 'Gateway Timeout';
            case self::HTTP_STATUS_200:
            default:
                return 'OK';
        }
    }

    /**
     * @param array $parameters
     */
    public function setRedirectionParameters(array $parameters): void
    {
        $this->redirectionParameters = $parameters;
    }

    /**
     * @return array
     */
    public function getRedirectionParameters(): array
    {
        return $this->redirectionParameters;
    }

    /**
     * @return string|null
     */
    public function redirects(): ?string
    {
        return $this->redirection;
    }

    /**
     * @param string $modelName
     */
    public function setRedirect(string $modelName): void
    {
        $this->redirection = $modelName;
    }
}
