<?php
namespace carlonicora\minimalism\core\controllers\abstracts;

use carlonicora\minimalism\core\controllers\interfaces\controllerInterface;
use carlonicora\minimalism\core\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\models\abstracts\abstractModel;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\paths\factories\serviceFactory;
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

    /**
     * abstractController constructor.
     * @param servicesFactory $services
     * @param string $modelName
     * @param array $parameterValueList
     * @param array $parameterValues
     * @throws Exception
     */
    public function __construct(servicesFactory $services, string $modelName=null, array $parameterValueList=null, array $parameterValues=null){
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
     * @return string
     */
    abstract public function render(): string;

    /**
     * @param array $parameterValueList
     * @param array $parameterValues
     * @throws Exception
     */
    protected function initialiseParameters(array $parameterValueList, array $parameterValues): void {
        if (!empty($parameterValueList) || !empty($parameterValues)) {
            $this->passedParameters = array_merge($parameterValueList, $parameterValues);
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
                            $this->passedParameters = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
                        } catch (Exception $e) {
                            $this->passedParameters = [];
                        }
                    }

                    if (isset($_FILES) && count($_FILES) === 1) {
                        $this->file = array_values($_FILES)[0];
                    }

                    foreach ($_POST as $parameter => $value) {
                        $this->passedParameters[$parameter] = $value;
                    }

                    break;
            }
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

        if (!(isset($uri) && $uri === '/')) {
            $variables = array_filter(explode('/', substr($uri, 1)), 'strlen');

            $isModelVariable = true;
            foreach ($variables as $variable) {
                if ($isModelVariable && !is_numeric($variable)) {
                    $this->modelName = str_replace('-', '\\', $variable);
                } else {
                    $this->passedParameters[] = $variable;
                }
                $isModelVariable = false;
            }
        }
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
            $paths = $this->services->service(serviceFactory::class);

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

        $this->model = new $modelClass($this->services, $this->passedParameters, $verb, $this->file);

        if ($this->model->redirect() !== ''){
            $this->initialiseModel($this->model->redirect());
        }
    }
}