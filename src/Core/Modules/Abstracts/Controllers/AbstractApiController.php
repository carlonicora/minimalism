<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;

abstract class AbstractApiController extends AbstractController {
    /** @var string */
    public string $verb;

    /**
     * abstractController constructor.
     * @param ServicesFactory $services
     * @param string $modelName
     * @param array $parameterValueList
     * @param array $parameterValues
     * @throws Exception
     */
    public function __construct(ServicesFactory $services, string $modelName=null, array $parameterValueList=null, array $parameterValues=null){
        $this->initialiseVerb();

        parent::__construct($services, $modelName, $parameterValueList, $parameterValues);
    }

    /**
     * @return string
     */
    protected function getHttpType(): string {
        return $this->verb;
    }

    /**
     *
     */
    protected function initialiseVerb(): void {
        $this->verb = $_SERVER['REQUEST_METHOD'];
        if ($this->verb === 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'DELETE') {
                $this->verb = 'DELETE';
            } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PUT') {
                $this->verb = 'PUT';
            }
        }
    }

    /**
     * @param string|null $modelName
     * @param string|null $verb
     * @throws Exception
     */
    protected function initialiseModel(string $modelName = null, string $verb = null): void {
        parent::initialiseModel($modelName, $this->verb);
    }
}