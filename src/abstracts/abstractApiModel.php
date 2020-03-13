<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\dataObjects\responseObject;
use carlonicora\minimalism\dataObjects\errorObject;
use carlonicora\minimalism\interfaces\responseInterface;

abstract class abstractApiModel extends abstractModel{

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

    public function __construct($configurations, $parameterValues, $parameterValueList, $verb, $file=null){
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
     * @return responseInterface
     */
    public function DELETE(): responseInterface {
        return new errorObject(responseObject::HTTP_STATUS_405);
    }

    /**
     * @return responseInterface
     */
    public function GET(): responseInterface {
        return new errorObject(responseObject::HTTP_STATUS_405);
    }

    /**
     * @return responseInterface
     */
    public function POST(): responseInterface {
        return new errorObject(responseObject::HTTP_STATUS_405);
    }

    /**
     * @return responseInterface
     */
    public function PUT(): responseInterface {
        return new errorObject(responseObject::HTTP_STATUS_405);
    }
}