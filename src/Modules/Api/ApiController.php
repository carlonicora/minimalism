<?php
namespace CarloNicora\Minimalism\Modules\Api;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Events\MinimalismInfoEvents;
use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractApiController;
use CarloNicora\Minimalism\Core\Modules\ErrorController;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ApiModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Traits\HttpHeadersTrait;
use Exception;

class ApiController extends AbstractApiController {
    use HttpHeadersTrait;

    /** @var ModelInterface|ApiModelInterface|ApiModel  */
    protected ModelInterface $model;

    /**
     * @param ModelInterface|string|null $modelName
     * @param string $verb
     * @return ControllerInterface
     * @throws Exception
     */
    public function initialiseModel($modelName = null, string $verb='GET'): ControllerInterface
    {
        $response = parent::initialiseModel($modelName, $this->verb);

        if ($this->model !== null){
            /**
             * @todo restrict to model instances of type ApiModel?
             *       setIncludedResourceTypes & setRequiredFields are defined in ApiModel
             */
            foreach ($this->passedParameters as $parameterKey=>$parameter) {
                if ($parameterKey === 'include') {
                    $this->model->setIncludedResourceTypes(explode(',', $parameter));
                } elseif ($parameterKey === 'fields') {
                    if (is_array($parameter)){
                        $requiredFields = [];

                        foreach ($parameter as $RequiredFieldsType=>$requiredFieldsValue){
                            $requiredFields[$RequiredFieldsType] = explode(',', $requiredFieldsValue);
                        }
                        $this->model->setRequiredFields($requiredFields);
                    }
                }
            }
        }

        return $response;
    }

    /**
     * @return ControllerInterface
     */
    public function postInitialise() : ControllerInterface
    {
        $errorController = null;
        try {
            $errorController = new ErrorController($this->services);

            $url = $_SERVER['REQUEST_URI'];

            if ($this->security !== null) {
                if ($this->security->isSignatureValid($this->verb, $url, $this->bodyParameters)) {
                    $this->services->logger()->info()->log(MinimalismInfoEvents::SECURITY_CHECK_PASSED());
                } else {
                    $this->services->logger()->error()
                        ->log(MinimalismErrorEvents::SECURITY_VALIDATION_FAILED())
                        ->throw(Exception::class, 'Unauthorised');
                }
            }

            $errorController = null;
        } catch (Exception $e) {
            $errorController->setException($e);
        }

        return $errorController ?? $this;
    }

    /**
     * @return ResponseInterface
     * @noinspection PhpRedundantCatchClauseInspection
     * @throws Exception
     */
    public function render(): ResponseInterface {
        try {
            $this->model->preRender();

            $response = $this->model->{$this->verb}();

            $this->model->postRender($response);

            $this->services->logger()->info()->log(MinimalismInfoEvents::MODEL_RUN($this->verb));
        } catch (Exception $e) {
            /**
             * @todo only supports models that extend ApiModel or use JsonApiModelTrait
             */
            $response=$this->model->generateResponseFromError($e);
        }

        /**
         * @todo completeRender expects int but ResponseInterface->getStatus returns string
         */
        $this->completeRender((int)$response->getStatus(), $response->getData());

        return $response;
    }
}
