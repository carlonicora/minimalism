<?php
namespace CarloNicora\Minimalism;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\Error;
use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Exceptions\MinimalismException;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
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

    /** @var HttpCode  */
    private HttpCode $httpResponseCode=HttpCode::Ok;

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

        if ($this->viewName !== null && ($transformer = $this->factories->getServiceFactory()->getTranformerService()) !== null){
            $this->contentType = $transformer->getContentType();

            try {
                $response = $transformer->transform(
                    document: $data,
                    viewFile: $this->viewName,
                );
            } catch (Exception| Throwable $e) {
                $this->sendException(
                    new MinimalismException(
                        status: HttpCode::InternalServerError,
                        message: $e->getMessage(),
                    )
                );
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

                $this->httpResponseCode = $model->run();

                if ($this->httpResponseCode === HttpCode::TemporaryRedirect){
                    $parameters = $model->getRedirectionParameters();
                    $modelName = $model->getRedirection();
                    $function = $model->getRedirectionFunction();
                }
            } while ($this->httpResponseCode === HttpCode::TemporaryRedirect);

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
        } catch (MinimalismException $e) {
            $this->sendException($e);
            exit;
        } catch (Exception $e) {
            $this->sendException(
                new MinimalismException(
                    status: HttpCode::tryFrom($e->getCode()) ?? HttpCode::InternalServerError,
                    message: $e->getMessage(),
                )
            );
            exit;
        } catch (Throwable $e){
            $this->sendException(
                new MinimalismException(
                    status: HttpCode::InternalServerError,
                    message: $e->getMessage(),
                )
            );
            exit;
        }
    }

    /**
     * @param MinimalismException $exception
     */
    private function sendException(
        MinimalismException $exception,
    ): void
    {
        $this->httpResponseCode = $exception->getStatus();

        $data = new Document();
        $data->addError(
            new Error(
                httpStatusCode: $exception->getHttpCode(),
                id: $exception->getId(),
                errorUniqueCode: $exception->getCode(),
                title: $exception->getMessage(),
            )
        );


        if (is_a($exception, Exception::class)){
            $data->addError(
                new Error(
                    e: $exception,
                    httpStatusCode: (string)$this->httpResponseCode->value,
                    detail: $exception->getMessage(),
                )
            );
        } else {
            $data->addError(
                new Error(
                    httpStatusCode: (string)$this->httpResponseCode->value,
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
            if ($this->httpResponseCode->value >= HttpCode::InternalServerError->value) {
                $this->factories->getServiceFactory()->getLogger()->emergency(
                    message: $exception->getMessage(),
                    context: [
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'url' => $this->factories->getServiceFactory()->getPath()->getUri() ?? '',
                        'exception' => $exception->getTrace(),
                        'responseCode' => $this->httpResponseCode->value,
                    ]
                );
            } else {
                $this->factories->getServiceFactory()->getLogger()->error(
                    message: $exception->getMessage(),
                    context: [
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'url' => $this->factories->getServiceFactory()->getPath()->getUri() ?? '',
                        'exception' => $exception->getTrace(),
                        'responseCode' => $this->httpResponseCode->value,
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

            header($this->httpResponseCode->getHttpResponseHeader());
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

        if (function_exists('fastcgi_finish_request') && $this->factories->getServiceFactory()->getPath()->getUri() !== null) {
            fastcgi_finish_request();
        }
    }
}