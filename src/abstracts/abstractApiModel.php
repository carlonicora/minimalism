<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\businessObjects\abstracts\abstractBusinessObject;
use carlonicora\minimalism\businessObjects\abstracts\abstractBusinessObjectsArray;
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
     * @return responseObject
     */
    private function methodNotAllowed() : errorObject {
        return new errorObject(responseObject::HTTP_STATUS_405);
    }

    /**
     * @return responseObject
     */
    public function DELETE(): abstractResponseObject {
        return $this->methodNotAllowed();
    }

    /**
     * @return responseObject
     */
    public function GET(): abstractResponseObject {
        return $this->methodNotAllowed();
    }

    /**
     * @return responseObject
     */
    public function POST(): abstractResponseObject {
        return $this->methodNotAllowed();
    }

    /**
     * @return responseObject
     */
    public function PUT(): abstractResponseObject {
        return $this->methodNotAllowed();
    }
}