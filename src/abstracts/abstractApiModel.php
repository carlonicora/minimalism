<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\businessObjects\abstracts\abstractBusinessObject;
use carlonicora\minimalism\businessObjects\abstracts\abstractBusinessObjectsArray;
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

    /** @var abstractBusinessObject */
    protected abstractBusinessObject $businessObject;

    /** @var abstractBusinessObjectsArray */
    protected abstractBusinessObjectsArray $arrayBusinessObject;

    /**
     * @inheritDoc
     */
    public function __construct(abstractConfigurations $configurations, array $parameterValues, array $parameterValueList, string $verb, array $file=null) {
        $this->verb = $verb;
        parent::__construct($configurations, $parameterValues, $parameterValueList, $file);
        // TODO implment trully dependency injection and catch configuration execption in the factory
        $this->initialiseBusinessObjects();
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

    protected function initialiseBusinessObjects(): void
    {
        $position = strpos(static::class, 'models');
        $businessObjectClass = substr_replace(static::class, 'businessObjects', $position, strlen('models'));
        $arrayBusinessObject = $businessObjectClass . 'Array';

        $this->businessObject = new $businessObjectClass;
        $this->arrayBusinessObject = new $arrayBusinessObject($this->businessObject);
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