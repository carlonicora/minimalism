<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\helpers\errorReporter;

abstract class abstractApiModel extends abstractModel {

    /** @var bool */
    protected $requiresAuthDELETE=false;

    /** @var bool */
    protected $requiresAuthGET=false;

    /** @var bool */
    protected $requiresAuthPOST=false;

    /** @var bool */
    protected $requiresAuthPUT=false;

    /** @var string */
    public $verb;


    public function __construct($configurations, $parameterValues, $parameterValueList, $file=null, $verb=null) {
        parent::__construct($configurations, $parameterValues, $parameterValueList, $file);
        $this->verb = $verb;
    }
    /**
     * @return array
     */
    protected function getParameters(): array {
        return $this->parameters[$this->verb];
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