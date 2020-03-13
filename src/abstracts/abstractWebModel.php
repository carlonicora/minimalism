<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\interfaces\responseInterface;

abstract class abstractWebModel extends abstractModel {
    /** @var string */
    protected string $viewName='';

    /**
     * abstractWebModel constructor.
     * @param $configurations
     * @param $parameterValues
     * @param $parameterValueList
     * @param null $file
     */
    public function __construct($configurations, $parameterValues, $parameterValueList, $file = null){
        parent::__construct($configurations, $parameterValues, $parameterValueList, $file);

        $this->response->addMeta('url', $this->configurations->getBaseUrl());
    }

    /**
     * @return responseInterface
     */
    public function generateData(): responseInterface{
        return $this->response;
    }

    /**
     * @return string
     */
    public function getViewName(): string {
        return $this->viewName;
    }
}