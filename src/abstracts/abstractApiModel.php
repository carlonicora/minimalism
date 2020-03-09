<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\helpers\errorReporter;

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
     * @return array
     */
    public function DELETE(): array {
        errorReporter::returnHttpCode(405, 'DELETE method not allowed');
        exit;
    }

    /**
     * @return array
     */
    public function GET(): array {
        errorReporter::returnHttpCode(405, 'GET method not allowed');
        exit;
    }

    /**
     * @return array
     */
    public function POST(): array {
        errorReporter::returnHttpCode(405, 'POST method not allowed');
        exit;
    }

    /**
     * @return array
     */
    public function PUT(): array {
        errorReporter::returnHttpCode(405, 'PUT method not allowed');
        exit;
    }
}