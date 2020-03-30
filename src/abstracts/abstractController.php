<?php
namespace carlonicora\minimalism\abstracts;

use carlonicora\minimalism\helpers\errorReporter;
use Exception;

abstract class abstractController {
    /** @var string */
    protected string $modelName = 'index';

    /** @var abstractConfigurations */
    protected abstractConfigurations $configurations;

    /** @var abstractModel */
    protected abstractModel $model;

    /** @var array */
    protected ?array $parameterValues = [];

    /** @var array */
    protected ?array $parameterValueList = [];

    /** @var array */
    protected ?array $file=null;

    /** @var string */
    public string $version;

    /**
     * abstractController constructor.
     * @param abstractConfigurations $configurations
     * @param string $modelName
     * @param array $parameterValueList
     * @param array $parameterValues
     */
    public function __construct(abstractConfigurations $configurations, string $modelName=null, array $parameterValueList=null, array $parameterValues=null){
        $this->configurations = $configurations;

        if (!empty($parameterValueList) || !empty($parameterValues)) {
            $this->parameterValueList = $parameterValueList;
            $this->parameterValues = $parameterValues;
        } else {
            $this->initialiseParameters();
        }

        $this->initialiseModel($modelName);
    }

    /**
     * @return string
     */
    abstract public function render(): string;

    /**
     *
     */
    protected function initialiseParameters(): void {
        $this->parseUriParameters();

        switch ($this->getHttpType()) {
            case 'GET':
                foreach ($_GET as $parameter => $value) {
                    if ($parameter !== 'path' && $parameter !== 'XDEBUG_SESSION_START') {
                        $this->parameterValues[$parameter] = $value;
                    }
                }

                break;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $input = file_get_contents('php://input');

                if (!empty($input)) {
                    try {
                        $this->parameterValues = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
                    } catch (Exception $e) {
                        $this->parameterValues = null;
                    }
                }

                if (isset($_FILES) && count($_FILES) === 1) {
                    $this->file = array_values($_FILES)[0];
                }

                foreach ($_POST as $parameter => $value) {
                    $this->parameterValues[$parameter] = $value;
                }

                break;
        }

    }

    /**
     * @return string
     */
    protected function getHttpType(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     *
     */
    protected function parseUriParameters(): void {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');

        if ($uri !== '/') {
            $variables = array_filter(explode('/', substr($uri, 1)), 'strlen');

            $this->parameterValueList = $this->parseModelNameFromUri($variables);
        }
    }

    /**
     * @param array $uriVariables
     * @return array
     */
    protected function parseModelNameFromUri(array $uriVariables): array {
        $firstArgument = current($uriVariables);
        if (false === $firstArgument || is_numeric($firstArgument)) {
            return $uriVariables;
        }

        $configurationClassName = get_class($this->configurations);
        $lastDashPosition = strrpos($configurationClassName, '\\');
        $basePath = substr($configurationClassName, 0, $lastDashPosition) . '\\models\\';

        $this->modelName = array_shift($uriVariables);
        foreach ($uriVariables as $uriParam) {
            if (is_dir($this->configurations->appDirectory . '\\models\\' . $this->modelName . '\\' . $uriParam)
                || class_exists($basePath . $this->modelName . '\\' . $uriParam))
            {
                $this->modelName     .= '\\' . $uriParam;
                array_shift($uriVariables);
            }
        }

        if (!class_exists($basePath . $this->modelName)) {
            errorReporter::report($this->configurations, 3, null, 404);
        }

        return $uriVariables;
    }

    /**
     * @param string|null $modelName
     * @param string|null $verb
     */
    protected function initialiseModel(string $modelName = null, string $verb=null): void {
        if (isset($modelName)) {
            $this->modelName = str_replace('-', '\\', $modelName);
        }

        $configurationClassName = get_class($this->configurations);
        $lastDashPosition = strrpos($configurationClassName, '\\');
        $modelClass = substr_replace($configurationClassName, '\\models\\' . $this->modelName, $lastDashPosition);

        if (!class_exists($modelClass)){
            errorReporter::report($this->configurations, 3, null, 404);
        }

        $this->model = new $modelClass($this->configurations, $this->parameterValues, $this->parameterValueList, $verb, $this->file);

        if (false === empty($this->model->redirectPage)){
            $this->initialiseModel($this->model->redirectPage);
        }
    }
}