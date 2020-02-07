<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\exceptions\requiredParameterException;

abstract class abstractModel {
    /** @var abstractConfigurations */
    protected $configurations;

    /** @var array */
    protected $parameterValues;

    /** @var array */
    protected $parameterValueList;

    /** @var array */
    protected $file;

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

    /** @var array */
    //protected $parameters = [];

    /** @var string */
    protected $definition;

    /**
     * model constructor.
     * @param abstractConfigurations $configurations
     * @param array $parameterValues
     * @param array $parameterValueList
     * @param array $file
     * @throws requiredParameterException
     */
    public function __construct($configurations, $parameterValues, $parameterValueList, $file=null){
        $this->configurations = $configurations;
        $this->parameterValues = $parameterValues;
        $this->parameterValueList = $parameterValueList;
        $this->file = $file;

        $this->buildParameters();

        $this->redirectPage = null;
    }

    /**
     * @throws requiredParameterException
     */
    private function buildParameters(): void{
        if ($this->definition !== null) {
            $definitions = explode('/', $this->definition);
            foreach ($definitions as $definitionKey=>$definitionValue){
                if (($definitionKey > 1) && strpos($definitionValue, '{') === 0) {
                    preg_match('#{$(.*?)}#', $definitionValue, $variableNames);
                    $variableName = $variableNames[1];

                    if (array_key_exists($variableName, $this->parameterValues)) {
                        $this->$variableName = $this->parameterValues[$variableName];
                    } else if (array_key_exists(($definitionKey-2), $this->parameterValueList)){
                        $this->$variableName = $this->parameterValueList[($definitionKey-2)];
                    } else if (substr($definitionValue, strlen($definitionValue) - 1) === '*'){
                        throw new requiredParameterException('Required parameter' . $variableName . ' missing.');
                    }
                }
            }
        }

        /*
        foreach ($this->parameters as $parameter) {
            if (array_key_exists($parameter['name'], $this->parameterValues)) {
                $this->{$parameter['name']} = $this->parameterValues[$parameter['name']];
            } else if (array_key_exists('order', $parameter) && array_key_exists($parameter['order'], $this->parameterValueList)){
                $this->{$parameter['name']} = $this->parameterValueList[$parameter['order']];
            } else if (array_key_exists('isRequired', $parameter) && $parameter['isRequired']) {
                throw new requiredParameterException('Required parameter' . $parameter['name'] . ' missing.');
            }
        }
        */
    }

    /**
     * @param $verb
     * @return mixed
     */
    public function requiresAuth($verb): bool {
        $authName = 'requiresAuth' . $verb;

        return $this->$authName;
    }

    /**
     * @return string
     */
    public function redirect(): string {
        return $this->redirectPage ?? '';
    }
}