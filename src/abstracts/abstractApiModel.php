<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\dataObjects\apiResponse;
use carlonicora\minimalism\dataObjects\errorObject;

abstract class abstractApiModel extends abstractModel {

    /** @var bool */
    protected bool $requiresAuthDELETE=false;

    /** @var bool */
    protected bool $requiresAuthGET=false;

    /** @var bool */
    protected bool $requiresAuthPOST=false;

    /** @var bool */
    protected bool $requiresAuthPUT=false;

    /** @var string */
    public string $verb='GET';

    /**
     * @inheritDoc
     */
    public function __construct(abstractConfigurations $configurations, array $parameterValues, array $parameterValueList, string $verb, array $file=null) {
        $this->verb = $verb;
        parent::__construct($configurations, $parameterValues, $parameterValueList, $file);
    }
    /**
     * @return array
     */
    protected function getParameters(): array {
        if (array_key_exists($this->verb, $this->parameters)){
            return $this->parameters[$this->verb];
        }

        return [];
    }

    /**
     * @param $verb
     * @return mixed
     */
    public function requiresAuth($verb): bool {
        $authName = 'requiresAuth' . $verb;

        return $this->$authName;
    }

    /**
     * @return apiResponse
     */
    private function methodNotAllowed() : apiResponse {
        $response = new apiResponse();
        $error = new errorObject(apiResponse::HTTP_STATUS_405);

        $response->addError($error);

        return $response;
    }

    /**
     * @return apiResponse
     */
    public function DELETE(): apiResponse {
        return $this->methodNotAllowed();
    }

    /**
     * @return apiResponse
     */
    public function GET(): apiResponse {
        return $this->methodNotAllowed();
    }

    /**
     * @return apiResponse
     */
    public function POST(): apiResponse {
        return $this->methodNotAllowed();
    }

    /**
     * @return apiResponse
     */
    public function PUT(): apiResponse {
        return $this->methodNotAllowed();
    }
}