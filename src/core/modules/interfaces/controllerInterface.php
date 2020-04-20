<?php
namespace carlonicora\minimalism\core\modules\interfaces;

use carlonicora\minimalism\core\services\factories\servicesFactory;
use Exception;
use Throwable;

interface controllerInterface {
    /**
     * abstractController constructor.
     * @param servicesFactory $services
     * @param string $modelName
     * @param array $parameterValueList
     * @param array $parameterValues
     * @throws Exception
     */
    public function __construct(servicesFactory $services, string $modelName=null, array $parameterValueList=null, array $parameterValues=null);

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