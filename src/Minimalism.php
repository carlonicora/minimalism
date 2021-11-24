<?php
namespace CarloNicora\Minimalism;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\Error;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
use Composer\InstalledVersions;
use Exception;
use JsonException;
use PackageVersions\Versions;
use Throwable;

class Minimalism
{
    /** @var MinimalismFactories  */
    private MinimalismFactories $factories;

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
    public function __construct(
    )
    {
        if (PHP_SAPI !== 'cli') {
            $this->startSession();
        }

        $this->factories = new MinimalismFactories();
    }

    /**
     *
     */
    public function __destruct()
    {
        session_write_close();
    }

    /**
     * @param string $serviceName
     * @return ServiceInterface
     * @throws Exception
     */
    public function getService(
        string $serviceName,
    ): ServiceInterface
    {
        return $this->factories->getServiceFactory()->create(
            className: $serviceName
        );
    }

    /**
     * @return MinimalismFactories
     */
    public function getMinimalismFactories(
    ): MinimalismFactories
    {
        return $this->factories;
    }

    /**
     *
     */
    private function startSession() : void
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (isset($_COOKIE['PHPSESSID'])) {
                $sessid = '';

                if (ini_get('session.use_cookies')) {
                    $sessid = $_COOKIE['PHPSESSID'];
                } elseif (!ini_get('session.use_only_cookies')) {
                    $sessid = $_GET['PHPSESSID'];
                }

                if (!preg_match('/^[a-z0-9]{32}$/', $sessid)) {
                    return;
                }
            }

            session_start();
        }
    }

    /**
     * @param string|null $modelName
     */
    public function render(
        ?string $modelName=null,
    ): void
    {
        $data = $this->generateData($modelName);

        /** @var TransformerInterface $transformer */
        if ($this->viewName !== null && ($transformer = $this->factories->getServiceFactory()->getTranformerService()) !== null){
            $this->contentType = $transformer->getContentType();

            try {
                $response = $transformer->transform(
                    document: $data,
                    viewFile: $this->viewName,
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
                $model = $this->factories->getModelFactory()->create(
                    modelName: $modelName,
                    parameters: $parameters,
                    function: $function,
                );

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
        if ($this->httpResponseCode === 0){
            $this->httpResponseCode = 500;
        }

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

        if ($this->factories->getServiceFactory()->getLogger() !== null) {
            if ($this->httpResponseCode > 500) {
                $this->factories->getServiceFactory()->getLogger()?->emergency(
                    message: $exception->getMessage(),
                    context: [
                        'file' => $exception->getFile() ?? '',
                        'line' => $exception->getLine(),
                        'url' => $this->factories->getServiceFactory()->getPath()->getUri() ?? '',
                        'exception' => $exception->getTrace(),
                        'responseCode' => $this->httpResponseCode,
                    ]
                );
            } else {
                $this->factories->getServiceFactory()->getLogger()?->error(
                    message: $exception->getMessage(),
                    context: [
                        'file' => $exception->getFile() ?? '',
                        'line' => $exception->getLine(),
                        'url' => $this->factories->getServiceFactory()->getPath()->getUri() ?? '',
                        'exception' => $exception->getTrace(),
                        'responseCode' => $this->httpResponseCode,
                    ]
                );
            }
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

        if ($this->factories->getServiceFactory()->getPath()->getUrl() !== null) {
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

        if ($this->factories->getServiceFactory()->getPath()->getUri() !== null) {
            fastcgi_finish_request();
        }
    }
}