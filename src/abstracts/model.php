<?php
namespace minimalism\abstracts;

abstract class model {
    /** @var configurations */
    protected $configurations;

    /** @var array */
    protected $parameterValues;

    /** @var string */
    protected $viewName;

    /** @var array */
    public $data;

    /** @var string */
    public $redirectPage;

    /**
     * model constructor.
     * @param configurations $configurations
     * @param array $parameterValues
     */
    public function __construct($configurations, $parameterValues){
        $this->configurations = $configurations;
        $this->parameterValues = $parameterValues;

        $this->data = array();
        $this->redirectPage = null;
    }

    public function getViewName(){
        $returnValue = $this->viewName;

        if (!isset($returnValue)) $returnValue = '';

        return($returnValue);
    }

    public function redirect(){
        if (isset($this->redirectPage)) return($this->redirectPage);

        return(false);
    }

    public abstract function run();
}