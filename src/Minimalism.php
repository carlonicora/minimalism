<?php
namespace CarloNicora\Minimalism;

use CarloNicora\Minimalism\Factories\ModelFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use Exception;

class Minimalism
{
    /** @var ServiceFactory  */
    private ServiceFactory $services;

    public function __construct()
    {
        $this->services = new ServiceFactory();

        //$this->services->create(TestServiceTwo::class);
    }

    /**
     * @param string|null $modelName
     * @return string
     */
    public function render(?string $modelName=null): string
    {
        $modelFactory = new ModelFactory($this->services);

        try {
            $model = $modelFactory->create($modelName);
            $response = $model->run();
        } catch (Exception $e) {
            $response = $e->getCode() ?? 500;
        }

        header($this->getProtocol() . ' ' . $response . ' ' . $this->generateStatusText($response));

        if ($response !== 200){
            exit;
        }

        //TODO: merge view

        return '';
    }

    /**
     * @return string
     */
    private function getProtocol() : string
    {
        return ($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1');
    }

    /**
     * @param int $status
     * @return string
     */
    private function generateStatusText(int $status) : string
    {
        switch ($status) {
            case 201:
                return 'Created';
            case 204:
                return 'No Content';
            case 304:
                return 'Not Modified';
            case 400:
                return 'Bad Request';
            case 401:
                return 'Unauthorized';
            case 403:
                return 'Forbidden';
            case 404:
                return 'Not Found';
            case 405:
                return 'Method Not Allowed';
            case 406:
                return 'Not Acceptable';
            case 409:
                return 'Conflict';
            case 410:
                return 'Gone';
            case 411:
                return 'Length Required';
            case 412:
                return 'Precondition Failed';
            case 415:
                return 'Unsupported Media Type';
            case 422:
                return 'Unprocessable Entity';
            case 428:
                return 'Precondition Required';
            case 429:
                return 'Too Many Requests';
            case 500:
                return 'Internal Server Error';
            case 501:
                return 'Not Implemented';
            case 502:
                return 'Bad Gateway';
            case 503:
                return 'Service Unavailable';
            case 504:
                return 'Gateway Timeout';
            case 200:
            default:
                return 'OK';
        }
    }
}