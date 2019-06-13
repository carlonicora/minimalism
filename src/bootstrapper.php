<?php
namespace carlonicora\minimalism;

use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\library\database\databaseFactory;

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

        session_start();

        if (isset($_SESSION['configurations'])){
            $this->configurations = $_SESSION['configurations'];
        } else {
            $this->configurations->loadConfigurations();
        }

        databaseFactory::initialise($this->configurations);
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
    }
}