<?php
namespace carlonicora\minimalism\core\modules\abstracts\models;

use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use Exception;

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
     * abstractApiModel constructor.
     * @param servicesFactory $services
     * @param array $passedParameters
     * @param string $verb
     * @param array|null $file
     * @throws serviceNotFoundException
     * @throws Exception
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
     * @return mixed
     */
    abstract public function DELETE();

    /**
     * @return mixed
     */
    abstract public function GET();

    /**
     * @return mixed
     */
    abstract public function POST();

    /**
     * @return mixed
     */
    abstract public function PUT();
}