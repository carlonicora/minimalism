<?php
namespace carlonicora\minimalism\controllers\interfaces;

use carlonicora\minimalism\services\factories\servicesFactory;

interface controllerInterface {
    /**
     * abstractController constructor.
     * @param servicesFactory $services
     * @param string $modelName
     * @param array $parameterValueList
     * @param array $parameterValues
     */
    public function __construct(servicesFactory $services, string $modelName=null, array $parameterValueList=null, array $parameterValues=null);

    /**
     * @return string
     */
    public function render() : string;
}