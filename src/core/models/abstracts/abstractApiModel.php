<?php
namespace carlonicora\minimalism\core\models\abstracts;

use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\jsonapi\interfaces\responseInterface;
use carlonicora\minimalism\core\jsonapi\responses\errorResponse;
use carlonicora\minimalism\core\services\factories\servicesFactory;

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

    /**
     * abstractApiModel constructor.
     * @param servicesFactory $services
     * @param array $passedParameters
     * @param string $verb
     * @param array|null $file
     * @throws serviceNotFoundException
     */
    public function __construct(servicesFactory $services, array $passedParameters, string $verb, array $file=null){
        $this->verb = $verb;
        parent::__construct($services, $passedParameters, $file);
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
        return new errorResponse(errorResponse::HTTP_STATUS_405);
    }

    /**
     * @return responseInterface
     */
    public function GET(): responseInterface {
        return new errorResponse(errorResponse::HTTP_STATUS_405);
    }

    /**
     * @return responseInterface
     */
    public function POST(): responseInterface {
        return new errorResponse(errorResponse::HTTP_STATUS_405);
    }

    /**
     * @return responseInterface
     */
    public function PUT(): responseInterface {
        return new errorResponse(errorResponse::HTTP_STATUS_405);
    }
}