<?php
namespace carlonicora\minimalism\core\modules\abstracts\controllers;

use carlonicora\minimalism\core\modules\abstracts\models\abstractModel;
use carlonicora\minimalism\core\modules\interfaces\controllerInterface;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\paths\paths;
use Exception;
use RuntimeException;

abstract class abstractController implements controllerInterface {
    /** @var string */
    protected string $modelName = 'index';

    /** @var servicesFactory */
    protected servicesFactory $services;

    /** @var abstractModel */
    protected abstractModel $model;

    /** @var array */
    protected array $passedParameters = [];

    /** @var array */
    protected ?array $file=null;

    /** @var string */
    public string $version;

    /** @var array  */
    protected array $bodyParameters = [];

    /** @var string */
    public string $verb;

    /**
     * abstractController constructor.
     * @param servicesFactory $services
     * @param string $modelName
     * @param array $parameterValueList
     * @param array $parameterValues
     * @throws Exception
     */
    public function __construct(servicesFactory $services, string $modelName=null, array $parameterValueList=null, array $parameterValues=null){
        $this->initialiseVerb();

        $this->services = $services;

        if ($parameterValueList === null){
            $parameterValueList = [];
        }

        if ($parameterValues === null){
            $parameterValues = [];
        }

        $this->initialiseParameters($parameterValueList, $parameterValues);
        $this->initialiseModel($modelName);
    }

    /**
     * @param array $parameterValueList
     * @param array $parameterValues
     * @throws Exception
     */
    protected function initialiseParameters(array $parameterValueList, array $parameterValues): void {
        if (!empty($parameterValueList) || !empty($parameterValues)) {
            $this->passedParameters = $parameterValueList;
            $this->bodyParameters = $parameterValues;
        } else {
            $this->parseUriParameters();

            switch ($this->getHttpType()) {
                case 'GET':
                    foreach ($_GET as $parameter => $value) {
                        if ($parameter !== 'path' && $parameter !== 'XDEBUG_SESSION_START') {
                            $this->passedParameters[$parameter] = $value;
                        }
                    }

                    break;
                case 'POST':
                case 'PUT':
                case 'DELETE':
                    $input = file_get_contents('php://input');

                    if (!empty($input)) {
                        try {
                            $this->bodyParameters = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
                        } catch (Exception $e) {
                            $this->bodyParameters = [];
                        }
                    }

                    if (isset($_FILES) && count($_FILES) === 1) {
                        $this->file = array_values($_FILES)[0];
                    }

                    foreach ($_POST as $parameter => $value) {
                        $this->bodyParameters[$parameter] = $value;
                    }

                    break;
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function getHttpType(): string {
        return $this->verb;
    }

    /**
     *
     */
    protected function initialiseVerb(): void {
        $this->verb = $_SERVER['REQUEST_METHOD'];
        if ($this->verb === 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'DELETE') {
                $this->verb = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PUT') {
                $this->verb = 'PUT';
            }
        }
    }

    /**
     *
     */
    protected function parseUriParameters(): void {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');

        if (!(isset($uri) && $uri === '/')) {
            $variables = array_filter(explode('/', substr($uri, 1)), 'strlen');

            $this->passedParameters = $this->parseModelNameFromUri($variables);
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

        $basePath = getcwd() . DIRECTORY_SEPARATOR . 'src'. DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR;

        $this->modelName = array_shift($uriVariables);
        foreach ($uriVariables as $uriParam) {
            $classPath = $basePath . $this->modelName . '\\' . $uriParam;
            if (is_dir($classPath) || is_file($classPath . '.php')) {
                $this->modelName .= '\\' . $uriParam;
                array_shift($uriVariables);
            }
        }

        return $uriVariables;
    }

    /**
     * @param string|null $modelName
     * @param string|null $verb
     * @throws Exception
     */
    protected function initialiseModel(string $modelName = null, string $verb=null): void {
        if (isset($modelName)) {
            $this->modelName = str_replace('-', '\\', $modelName);
        }

        try {
            /** @var paths $paths */
            $paths = $this->services->service(paths::class);

            $content = file_get_contents($paths->getRoot() . DIRECTORY_SEPARATOR . 'composer.json');
            $content = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            $namespace = key($content['autoload']['psr-4']);
        } catch (serviceNotFoundException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }

        $modelClass = $namespace . 'models\\' . $this->modelName;

        if (!class_exists($modelClass)){
            throw new RuntimeException('model ' . $this->modelName . ' not found', 404);
        }

        $this->model = new $modelClass($this->services, array_merge($this->passedParameters, $this->bodyParameters), $verb, $this->file);

        if ($this->model->redirect() !== ''){
            $this->initialiseModel($this->model->redirect());
        }
    }

    /**
     * @param Exception $e
     * @return void
     */
    abstract public function writeException(Exception $e): void;

    /**
     * @return string
     */
    abstract public function render(): string;
}