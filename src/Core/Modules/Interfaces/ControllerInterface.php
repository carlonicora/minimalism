<?php
namespace CarloNicora\Minimalism\Core\Modules\Interfaces;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;
use Throwable;

interface ControllerInterface {
    /**
     * abstractController constructor.
     * @param ServicesFactory $services
     * @param string $modelName
     * @param array $parameterValueList
     * @param array $parameterValues
     * @throws Exception
     */
    public function __construct(ServicesFactory $services, string $modelName=null, array $parameterValueList=null, array $parameterValues=null);

    /**
     * @return string
     */
    public function render() : string;

    /**
     * @param Throwable $e
     * @param string $httpStatusCode
     */
    public function writeException(Throwable $e, string $httpStatusCode = '500'): void;
}