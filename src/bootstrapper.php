<?php
namespace carlonicora\minimalism;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\helpers\sessionManager;

class bootstrapper{
    /** @var abstractConfigurations $configurations */
    private $configurations;

    /**
     * bootstrapper constructor.
     * @param string $configurationName
     */
    public function __construct($configurationName){
        $this->configurations = new $configurationName();

        $sessionManager = new sessionManager();
        $sessionManager->loadFromSession($this->configurations);
    }

    /**
     * @param null $modelName
     * @param null $parameterValueList
     * @param null $parameterValues
     * @return controller
     */
    public function loadController($modelName=null, $parameterValueList=null, $parameterValues=null): controller {
        $controller = new controller($this->configurations, $modelName, $parameterValueList, $parameterValues);

        return $controller;
    }

    /**
     * @return abstractConfigurations
     */
    public function getConfigurations(): abstractConfigurations{
        return $this->configurations;
    }
}