<?php
namespace carlonicora\minimalism;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\helpers\sessionManager;

class bootstrapper{
    /** @var abstractConfigurations $configurations */
    private $configurations;

    /** @var string $namespace */
    private $namespace;

    /**
     * bootstrapper constructor.
     * @param string $namespace
     */
    public function __construct($namespace){
        $this->namespace = $namespace;

        $this->initialiseConfigurations();
    }
    
    public function loadController($modelName=null, $parameterValueList=null, $parameterValues=null){
        $controller = new controller($this->configurations, $modelName, $parameterValueList, $parameterValues);

        return($controller);
    }

    /**
     * Initialises the configurations
     */
    private function initialiseConfigurations(){
        $configurationName = $this->namespace . "\\configurations";
        $this->configurations = new $configurationName($this->namespace);

        $sessionManager = new sessionManager();
        $sessionManager->loadFromSession($this->configurations);

        /*


        if (isset($_SESSION['configurations'])){
            $this->configurations = $_SESSION['configurations'];
        } else {
            $this->configurations->loadConfigurations();
            if (isset($_COOKIE['campaign_builder_keys'])){
                list($this->configurations->publicKey, $this->configurations->privateKey) = explode(';', $_COOKIE['campaign_builder_keys']);
            }
        }
        */
    }
}