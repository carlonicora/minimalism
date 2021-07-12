<?php
namespace CarloNicora\Minimalism;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\Error;
use CarloNicora\Minimalism\Factories\ModelFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use Composer\InstalledVersions;
use Exception;
use JsonException;
use PackageVersions\Versions;
use Throwable;

class Minimalism
{
    private const INITIALISE_SERVICES=1;
    private const INITIALISE_MODELS=2;

    /** @var ServiceFactory  */
    private ServiceFactory $services;

    /** @var ModelFactory  */
    private ModelFactory $modelFactory;

    /** @var string|null  */
    private ?string $viewName=null;

    /** @var string  */
    private string $contentType='text/plain';

    /** @var int  */
    private int $httpResponseCode=200;

    /** @var string[]  */
    private array $httpCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /**
     * Minimalism constructor.
     */
    public function __construct()
    {
        $this->services = new ServiceFactory();
        $this->modelFactory = new ModelFactory();
    }

    /**
     * @param string $serviceName
     * @param bool $requiresBaseService
     * @return ServiceInterface
     * @throws Exception
     */
    public function getService(
        string $serviceName,
        bool $requiresBaseService=true
    ): ServiceInterface
    {
        $this->initialise(
            self::INITIALISE_SERVICES,
            ['requiresBaseService' => $requiresBaseService]
        );

        return $this->services->create(
            serviceName: $serviceName
        );
    }

    /**
     * @param int $type
     * @param array|null $parameters
     */
    private function initialise(
        int $type,
        ?array $parameters=null,
    ): void
    {
        try {
            if ($type === self::INITIALISE_SERVICES) {
                $requiresBaseService = true;

                if ($parameters !== null && array_key_exists('requiresBaseService', $parameters)){
                    $requiresBaseService = $parameters['requiresBaseService'];
                }
                $this->services->initialise($requiresBaseService);
            } else {
                $this->modelFactory->initialise($this->services);
            }
        } catch (Exception|Throwable $e) {
            $this->httpResponseCode = 500;
            $this->sendException($e);

            $this->services->getLogger()->emergency(
                'Failed to initialise '
                . ($type === self::INITIALISE_SERVICES
                    ? 'services'
                    : 'models')
            );
            exit;
        }
    }

    /**
     * @param string|null $modelName
     */
    public function render(?string $modelName=null): void
    {
        $this->initialise(self::INITIALISE_SERVICES);
        $this->initialise(self::INITIALISE_MODELS);

        $data = $this->generateData($modelName);

        if ($this->viewName !== null
            && ($transformer = $this->services->getTransformer()) !== null
        ){
            $this->contentType = $transformer->getContentType();

            try {
                $response = $transformer->transform(
                    $data,
                    $this->viewName
                );
            } catch (Exception| Throwable $e) {
                $this->httpResponseCode = 500;
                $this->sendException($e);
                exit;
            }
        } else {
            try {
                $response = $data->export();
            } catch (JsonException) {
                $response = '';
            }
        }

        $this->send($response);
    }

    /**
     * @param string|null $modelName
     * @return Document
     */
    private function generateData(
        ?string $modelName,
    ): Document
    {
        $function = null;

        try {
            $parameters = null;
            do {
                $model = $this->modelFactory->create($modelName, $parameters, $function);

                if (($preRenderFunction = $model->getPreRenderFunction()) !== null){
                    $preRenderFunction();
                }

                $this->httpResponseCode = $model->run();

                if ($this->httpResponseCode === 302){
                    $parameters = $model->getRedirectionParameters();
                    $modelName = $model->getRedirection();
                    $function = $model->getRedirectionFunction();
                }
            } while ($this->httpResponseCode === 302);

            if (($postRenderFunction = $model->getPostRenderFunction()) !== null){
                $postRenderFunction();
            }

            $this->viewName = $model->getView();
            $data = $model->getDocument();
            $this->contentType = $data->getContentType();

            if ($includeParameter = $model->getParameterValue('include')) {
                $included = explode(',', $includeParameter);
                $data->setIncludedResourceTypes($included);
            }

            if ($fieldsParameter = $model->getParameterValue('fields')) {
                $fields = [];
                foreach ($fieldsParameter as $resourceType => $fieldList){
                    $fields[$resourceType] = explode(',', $fieldList);
                }
                $data->setRequiredFields($fields);
            }

            return $data;
        } catch (Exception $e) {
            $this->httpResponseCode = $e->getCode() ?? 500;
            $this->sendException($e);
            exit;
        } catch (Throwable $e){
            $this->httpResponseCode = 500;
            $this->sendException($e);
            exit;
        }
    }

    /**
     * @param Exception|Throwable $exception
     */
    private function sendException(Exception|Throwable $exception): void
    {
        $data = new Document();
        if (is_a($exception, Exception::class)){
            $data->addError(
                new Error(
                    e: $exception,
                    httpStatusCode: $this->httpResponseCode,
                    detail: $exception->getMessage(),
                )
            );
        } else {
            $data->addError(
                new Error(
                    httpStatusCode: $this->httpResponseCode,
                    detail: $exception->getMessage(),
                )
            );
        }

        try {
            $response = $data->export();
        } catch (JsonException) {
            $response = 'Error';
        }

        $this->send($response);

        if ($this->httpResponseCode > 500){
            $this->services->getLogger()->emergency(
                message: $exception->getMessage(),
                context: [
                    'file' => $exception->getFile() ?? '',
                    'line' => $exception->getLine() ?? '',
                    'url' => $this->services->getPath()->getUri()??'',
                    'exception' => $exception->getTrace(),
                ]
            );
        } else {
            $this->services->getLogger()->error(
                message: $exception->getMessage(),
                context: [
                    'file' => $exception->getFile() ?? '',
                    'line' => $exception->getLine() ?? '',
                    'url' => $this->services->getPath()->getUri()??'',
                    'exception' => $exception->getTrace(),
                ]
            );
        }
    }

    /**
     * @param string $response
     */
    private function send(
        string $response,
    ): void
    {
        if ($response === '{"meta":[]}'){
            $response = '';
        }

        if ($this->services->getPath()->getUrl() !== null) {
            header('Content-Type: ' . $this->contentType);

            header(
                ($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1')
                . ' ' . $this->httpResponseCode
                . ' ' . $this->httpCodes[$this->httpResponseCode]
            );
            header(
                'X-Minimalism-App: '
                . explode('/', Versions::rootPackageName())[1] . '/'
                . InstalledVersions::getPrettyVersion(Versions::rootPackageName())
            );
            header(
                'X-Minimalism: '
                . InstalledVersions::getPrettyVersion('carlonicora/minimalism')
            );
        }

        echo $response;

        if ($this->services->getPath()->getUri() !== null) {
            fastcgi_finish_request();
        }
    }
}