<?php
namespace carlonicora\minimalism;

use carlonicora\minimalism\abstracts\configurations;
use carlonicora\minimalism\abstracts\model;
use carlonicora\minimalism\helpers\errorReporter;

class controller {
    /** @var string */
    private $modelName;

    /** @var configurations */
    private $configurations;

    /** @var model */
    private $model;

    /** @var \Twig_Environment */
    private $view;

    /** @var array */
    private $parameterValues;

    public function __construct($configurations, $modelName=null, $parameters=null){
        $this->configurations = $configurations;

        if (isset($parameters)) {
            $this->parameterValues = $parameters;
        } else {
            $this->initialiseParameters();
        }

        if (isset($modelName)) $this->modelName = $modelName;

        $this->initialiseModel();
        $this->initialiseView();
    }

    public function render(){
        $data = array();
        $data['baseUrl'] = $this->configurations->getBaseUrl();

        if (!$this->model->run()) errorReporter::returnHttpCode(500);

        if (array_key_exists('forceRedirect', $this->model->data)){
            header('Location:' . $this->model->data['forceRedirect']);
            exit;
        }

        $data['page'] = $this->model->data;

        if ($this->model->getViewName() != ''){
            $returnValue = $this->view->render($data);
        } else {
            $returnValue = json_encode($data);
        }

        return($returnValue);
    }
    
    private function initialiseParameters(){
        $this->modelName = 'index';
        $this->parameterValues = array();

        $uri = strtok($_SERVER["REQUEST_URI"],'?');

        if (!(isset($uri) && strlen($uri) == 1 && $uri == '/')) {
            $variables = array_filter(explode('/', substr($uri, 1)), 'strlen');

            $isModelVariable = true;
            foreach($variables as $variable){
                if ($isModelVariable &&!(is_numeric($variable))){
                    $this->modelName = $variable;
                } else {
                    $this->parameterValues[] = $variable;
                }
                $isModelVariable = false;
            }
        }

        foreach ($_GET as $parameter=>$value){
            if ($parameter != 'path') $this->parameterValues[$parameter] = $value;
        }

        foreach ($_POST as $parameter=>$value){
            $this->parameterValues[$parameter] = $value;
        }
    }

    private function initialiseModel(){
        $this->modelName = str_replace('-', '\\', $this->modelName);

        $modelClass = $this->configurations->getNamespace() . '\\models\\' . $this->modelName;
        if (!class_exists($modelClass)){
            errorReporter::report($this->configurations, 3, null, 404);
        } else {
            $this->model = new $modelClass($this->configurations, $this->parameterValues);
        }

        if ($this->model->redirect() != false){
            $this->modelName = $this->model->redirect();
            $this->initialiseModel();
        }
    }

    private function initialiseView(){
        if ($this->model->getViewName() != '') {
            try {
                $twigLoader = new \Twig_Loader_Filesystem($this->configurations->appDirectory . DIRECTORY_SEPARATOR . 'views');
                $this->view = new \Twig_Environment($twigLoader);
            } catch (\Exception $exception) {
                errorReporter::report($this->configurations, 4, null, 404);
            }

            $this->view = $this->view->load($this->model->getViewName() . '.twig');

            if (!$this->view) {
                errorReporter::report($this->configurations, 5, null, 404);
            }
        }
    }
}