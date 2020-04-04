<?php
namespace carlonicora\minimalism\core\controllers;

use carlonicora\minimalism\core\bootstrapper;
use carlonicora\minimalism\core\controllers\abstracts\abstractController;
use carlonicora\minimalism\core\controllers\traits\httpHeaders;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\core\jsonapi\responses\dataResponse;
use carlonicora\minimalism\services\security\factories\serviceFactory;
use carlonicora\minimalism\services\security\security;
use Exception;

class apiController extends abstractController {
    use httpHeaders;

    /** @var string */
    private string $signature;

    /** @var string */
    public string $verb;

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

        if (bootstrapper::$servicesCache !== null){
            file_put_contents(bootstrapper::$servicesCache, serialize($this->services));
        }

        return $apiResponse->toJson();
    }
}