<?php
namespace carlonicora\minimalism;

use carlonicora\minimalism\abstracts\configurations;
use carlonicora\minimalism\helpers\errorReporter;

class bootstrapper{
    /** @var configurations $configurations */
    private $configurations;

    /** @var string $namespace */
    private $namespace;

    /**
     * bootstrapper constructor.
     * @param string $namespace
     */
    public function __construct($namespace){
        $this->namespace = $namespace;

        $this->initialiseDirectoryStructure();

        $this->initialiseConfigurations();

        session_start();

        if (isset($_SESSION['configurations'])){
            $this->configurations = $_SESSION['configurations'];
            $this->configurations->refreshConnections();
        } else {
            $this->configurations->loadConfigurations();
            $_SESSION['configurations'] = $this->configurations;
        }
    }

    public function loadController(){
        $controller = new controller($this->configurations);

        return($controller);
    }

    /**
     * Initialises the directory structure required by minimalism
     */
    private function initialiseDirectoryStructure(){
        $directoryLog = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'logs';

        if (!file_exists($directoryLog) && !mkdir($directoryLog)) errorReporter::returnHttpCode('Cannot create log directory');
    }

    /**
     * Initialises the configurations
     */
    private function initialiseConfigurations(){
        $configurationName = $this->namespace . "\\configurations";
        $this->configurations = new $configurationName($this->namespace);
    }
}