<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;

abstract class AbstractApiModel extends AbstractModel {
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
     * @param ServicesFactory $services
     * @param array $passedParameters
     * @param string $verb
     * @param array|null $file
     * @throws ServiceNotFoundException
     * @throws Exception
     */
    public function __construct(ServicesFactory $services, array $passedParameters, string $verb, array $file=null){
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