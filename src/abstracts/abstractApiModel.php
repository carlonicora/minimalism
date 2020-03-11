<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\dataObjects\responseObject;
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
     * @return responseObject
     */
    private function methodNotAllowed() : responseObject {
        $response = new responseObject();
        $error = new errorObject(responseObject::HTTP_STATUS_405);

        $response->addError($error);

        return $response;
    }

    /**
     * @return responseObject
     */
    public function DELETE(): responseObject {
        return $this->methodNotAllowed();
    }

    /**
     * @return responseObject
     */
    public function GET(): responseObject {
        return $this->methodNotAllowed();
    }

    /**
     * @return responseObject
     */
    public function POST(): responseObject {
        return $this->methodNotAllowed();
    }

    /**
     * @return responseObject
     */
    public function PUT(): responseObject {
        return $this->methodNotAllowed();
    }
}