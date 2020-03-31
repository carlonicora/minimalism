<?php
namespace carlonicora\minimalism\controllers;

use carlonicora\minimalism\controllers\abstracts\abstractController;
use carlonicora\minimalism\exceptions\serviceNotFoundException;
use carlonicora\minimalism\services\factories\servicesFactory;
use carlonicora\minimalism\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\jsonapi\responses\dataResponse;
use carlonicora\minimalism\services\security\factories\serviceFactory;
use carlonicora\minimalism\services\security\security;
use Exception;

class apiController extends abstractController {
    /** @var string */
    private string $signature;

    /** @var string */
    public string $verb;

    /** @var array|null */
    private ?array $headers=null;

    /**
     * apiController constructor.
     * @param servicesFactory $services
     * @param string|null $modelName
     * @param array|null $parameterValueList
     * @param array|null $parameterValues
     * @throws Exception
     */
    public function __construct(servicesFactory $services, string $modelName=null, array $parameterValueList=null, array $parameterValues=null){
        $this->initialiseVerb();

        parent::__construct($services, $modelName, $parameterValueList, $parameterValues);

        $this->validateSignature();
    }

    /**
     * @inheritDoc
     */
    protected function getHttpType(): string {
        return $this->verb;
    }

    /**
     *
     */
    protected function initialiseVerb(): void {
        $this->verb = $_SERVER['REQUEST_METHOD'];
        if ($this->verb === 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'DELETE') {
                $this->verb = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PUT') {
                $this->verb = 'PUT';
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function initialiseModel(string $modelName = null, string $verb=null): void {
        parent::initialiseModel($modelName, $this->verb);
    }

    /**
     *
     * @throws serviceNotFoundException
     * @throws Exception
     */
    protected function validateSignature(): void {
        /** @var security $security */
        $security = $this->services->service(serviceFactory::class);
        $this->signature =$this->getHeader($security->getHttpHeaderSignature());

        $url = $_SERVER['REQUEST_URI'];

        $security->validateSignature($this->signature, $this->verb, $url,  $this->passedParameters, $security->getSecurityClient(), $security->getSecuritySession());
    }

    /**
     *
     */
    protected function parseUriParameters(): void {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');

        if (!(isset($uri) && $uri === '/')) {
            $variables = array_filter(explode('/', substr($uri, 1)), 'strlen');

            $isModelVariable = true;
            foreach ($variables as $variable) {
                if ($isModelVariable && stripos($variable, 'v') === 0 && is_numeric(substr($variable, 1, 1)) && strpos($variable, '.') !== 0){
                    $this->version = $variable;
                } else if ($isModelVariable && !is_numeric($variable)) {
                    $this->modelName = str_replace('-', '\\', $variable);
                    $isModelVariable = false;
                } else {
                    $this->passedParameters[] = $variable;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function render(): string{
        $error = $this->model->preRender();
        if ($error !== null){
            return $error->toJson();
        }

        /** @var responseInterface $apiResponse */
        $apiResponse = $this->model->{$this->verb}();

        $code = $apiResponse->getStatus();
        $GLOBALS['http_response_code'] = $code;

        header(dataResponse::generateProtocol() . ' ' . $code . ' ' . $apiResponse->generateText());

        return $apiResponse->toJson();
    }

    /**
     * @param string $headerName
     * @return string|null
     */
    private function getHeader(string $headerName): ?string {
        if ($this->headers === null){
            $this->headers = getallheaders();
        }

        return $this->headers[$headerName] ?? null;
    }
}

/**
 *
 */
if (!function_exists('getallheaders'))  {
    function getallheaders()
    {
        if (!is_array($_SERVER)) {
            return array();
        }

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}