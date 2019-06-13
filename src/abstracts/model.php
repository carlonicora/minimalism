<?php
namespace carlonicora\minimalism\abstracts;

abstract class model {
    /** @var abstractConfigurations */
    protected $configurations;

    /** @var array */
    protected $parameterValues;

    /** @var array */
    protected $parameterValueList;

    /** @var array */
    protected $file;

    /** @var string */
    protected $viewName;

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
     * @param abstractConfigurations $configurations
     * @param array $parameterValues
     * @param array $parameterValueList
     * @param array $file
     */
    public function __construct($configurations, $parameterValues, $parameterValueList, $file=null){
        $this->configurations = $configurations;
        $this->parameterValues = $parameterValues;
        $this->parameterValueList = $parameterValueList;
        $this->file = $file;

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

    /**
     * @return array
     */
    public function generateData(){
        return(array());
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