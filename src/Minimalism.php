<?php
namespace CarloNicora\Minimalism;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\Error;
use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Exceptions\MinimalismException;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\TimerFactory;
use CarloNicora\Minimalism\Interfaces\ModelExtenderInterface;
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
        if (array_key_exists('REQUEST_URI', $_SERVER)){
            $fileExtension = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION);
            if (in_array(needle: $fileExtension, haystack: ['ico', 'png', 'jpg', 'css', 'js'])){
                header(HttpCode::NotFound->getHttpResponseHeader());
                exit;
            }
        }

        TimerFactory::start();
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
            $inCookies = false;
            if (isset($_COOKIE['PHPSESSID'])) {
                $inCookies = true;
                $sessionName = '';

                if (ini_get('session.use_cookies')) {
                    $sessionName = $_COOKIE['PHPSESSID'];
                } elseif (!ini_get('session.use_only_cookies')) {
                    $sessionName = $_GET['PHPSESSID'];
                }

                if (!preg_match('/^[a-z\d]{32}$/', $sessionName)) {
                    return;
                }
            }

            /** @noinspection NotOptimalIfConditionsInspection */
            if (session_start() && !$inCookies){
                setcookie('PHPSESSID', session_id(), time() + 60 * 60 * 24 * 365, '/', ini_get('session.cookie_domain'), !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', ini_get('session.cookie_httponly'));
            }
        }
    }

    /**
     * @param string|null $modelName
     */
    public function render(
        ?string $modelName=null,
    ): void
    {
        try{
            $this->factories->getServiceFactory()->postInitialise();

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
        } catch (MinimalismException $e) {
            $this->sendException($e);
            exit;
        } catch (Exception $e) {
            $this->sendException(
                exception: new MinimalismException(
                    status: HttpCode::tryFrom($e->getCode()) ?? HttpCode::InternalServerError,
                    message: $e->getMessage(),
                ),
                trace: $e,
            );
            exit;
        } catch (Throwable $e){
            $this->sendException(
                exception: new MinimalismException(
                    status: HttpCode::InternalServerError,
                    message: $e->getMessage(),
                ),
                trace: $e,
            );
            exit;
        }
    }

    /**
     * @param string|null $modelName
     * @return Document
     * @throws MinimalismException|Exception|Throwable
     */
    private function generateData(
        ?string $modelName,
    ): Document
    {
        $function = null;

        $parameters = null;
        do {
            $model = $this->factories->getModelFactory()->create(
                modelName: $modelName,
                parameters: $parameters,
                function: $function,
            );

            $extendedModel = null;
            $interfaces = class_implements($model);
            if ($interfaces !== false && array_key_exists(ModelExtenderInterface::class, $interfaces)){
                /** @var ModelExtenderInterface $model*/
                $extendedModel = $this->factories->getModelFactory()->create(
                    modelName: $model->getExtendedModel(),
                    parameters: $parameters,
                    function: $function,
                );

                $this->httpResponseCode = $extendedModel->run();

                $model->setExtendedModelResult(
                    document: $extendedModel->getDocument(),
                    response: $this->httpResponseCode,
                );
            }

            try {
                $this->httpResponseCode = $model->run();
            } catch (Exception $e) {
                if ($extendedModel !== null && $e->getCode() === HttpCode::NotImplemented->value){
                    $model = $extendedModel;
                } else {
                    throw $e;
                }
            }

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
    }

    /**
     * @param MinimalismException $exception
     * @param Exception|Throwable|null $trace
     */
    private function sendException(
        MinimalismException $exception,
        Exception|Throwable|null $trace=null,
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
                        'url' => $this->factories->getServiceFactory()->getPath()?->getUri() ?? '',
                        'exception' => $trace ? $trace->getTrace() : $exception->getTrace(),
                        'responseCode' => $this->httpResponseCode->value,
                    ]
                );
            } else {
                $this->factories->getServiceFactory()->getLogger()->error(
                    message: $exception->getMessage(),
                    context: [
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'url' => $this->factories->getServiceFactory()->getPath()?->getUri() ?? '',
                        'exception' => $trace ? $trace->getTrace() : $exception->getTrace(),
                        'responseCode' => $this->httpResponseCode->value,
                    ]
                );
            }
        } else {
            /** @noinspection ForgottenDebugOutputInspection */
            error_log(
                message: $exception->getMessage() . PHP_EOL . ($trace !== null ? $trace->getTraceAsString() : $exception->getTraceAsString()),
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

        if ($this->factories->getServiceFactory()->getPath()?->getUrl() !== null) {
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

        if ($this->httpResponseCode->value < 400) {
            $this->getMinimalismFactories()->getServiceFactory()->getLogger()?->info('request');
        } elseif ($this->httpResponseCode->value < 500) {
            $this->getMinimalismFactories()->getServiceFactory()->getLogger()?->error('request');
        } else {
            $this->getMinimalismFactories()->getServiceFactory()->getLogger()?->emergency('request');
        }

        if (function_exists('fastcgi_finish_request') && $this->factories->getServiceFactory()->getPath()?->getUri() !== null) {
            fastcgi_finish_request();
        }
    }
}