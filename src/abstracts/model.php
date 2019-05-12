<?php
namespace carlonicora\minimalism\abstracts;

abstract class model {
    /** @var configurations */
    protected $configurations;

    /** @var array */
    protected $parameterValues;

    /** @var array */
    protected $parameterValueList;

    /** @var string */
    protected $viewName;

    /** @var array */
    public $data;

    /** @var string */
    public $redirectPage;

    /** @var bool */
    protected $requiresAuthDELETE=false;

    /** @var bool */
    protected $requiresAuthGET=false;

    /** @var bool */
    protected $requiresAuthPOST=false;

    /** @var bool */
    protected $requiresAuthPUT=false;

    /**
     * model constructor.
     * @param configurations $configurations
     * @param array $parameterValues
     * @param array $parameterValueList
     */
    public function __construct($configurations, $parameterValues, $parameterValueList){
        $this->configurations = $configurations;
        $this->parameterValues = $parameterValues;
        $this->parameterValueList = $parameterValueList;

        $this->data = array();
        $this->redirectPage = null;
    }

    public function requiresAuth($verb){
        $authName = 'requiresAuth' . $verb;

        return($this->$authName);
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

    public function run(){
        return(true);
    }

    public function DELETE(){
        return(true);
    }

    public function GET(){
        return(true);
    }

    public function POST(){
        return(true);
    }

    public function PUT(){
        return(true);
    }
}