<?php
namespace carlonicora\minimalism\core\modules\abstracts\controllers;

use carlonicora\minimalism\core\services\factories\servicesFactory;
use Exception;

abstract class abstractWebController extends abstractController {

    /**
     * apiController constructor.
     * @param servicesFactory $services
     * @param string|null $modelName
     * @param array|null $parameterValueList
     * @param array|null $parameterValues
     * @throws Exception
     */
    public function __construct(servicesFactory $services, string $modelName=null, array $parameterValueList=null, array $parameterValues=null) {
        parent::__construct($services, $modelName, $parameterValueList, $parameterValues);

        $this->initialiseView();
    }

    /**
     *
     */
    abstract protected function initialiseView(): void;
}