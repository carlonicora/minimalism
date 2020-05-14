<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractModel;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Logger;
use CarloNicora\Minimalism\Services\Paths\Paths;
use Exception;
use JsonException;
use RuntimeException;

abstract class AbstractController implements ControllerInterface {
    /** @var string */
    protected string $modelName = 'index';

    /** @var ServicesFactory */
    protected ServicesFactory $services;

    /** @var AbstractModel */
    protected AbstractModel $model;

    /** @var array */
    protected array $passedParameters = [];

    /** @var array */
    protected ?array $file=null;

    /** @var string */
    public string $version;

    /** @var array  */
    protected array $bodyParameters = [];

    /** @var Logger  */
    protected Logger $logger;

    /**
     * abstractController constructor.
     * @param ServicesFactory $services
     * @throws Exception
     */
    public function __construct(ServicesFactory $services){
        $this->services = $services;

        $this->logger = $services->service(Logger::class);
    }

    /**
     * @param string|null $modelName
     * @param array|null $parameterValueList
     * @param array|null $parameterValues
     * @return ControllerInterface
     * @throws Exception
     */
    public function initialise(string $modelName=null, array $parameterValueList=null, array $parameterValues=null): ControllerInterface
    {
        if ($parameterValueList === null){
            $parameterValueList = [];
        }

        if ($parameterValues === null){
            $parameterValues = [];
        }

        $this->initialiseParameters($parameterValueList, $parameterValues);
        $this->logger->addSystemEvent(null, 'Parameters Initialised');

        $this->initialiseModel($modelName);
        $this->logger->addSystemEvent(null, 'Model Initialised');

        return $this;
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

        $response = [];
        $this->modelName = array_shift($uriVariables);
        foreach ($uriVariables as $uriParam) {
            $classPath = $basePath . $this->modelName . DIRECTORY_SEPARATOR . $uriParam;
            if (is_dir($classPath) || is_file($classPath . '.php')) {
                $this->modelName .= DIRECTORY_SEPARATOR . $uriParam;
            } else {
                $response[] = $uriVariables[0];
            }
            array_shift($uriVariables);
        }

        return $response;
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
            /** @var Paths $paths */
            $paths = $this->services->service(Paths::class);

            $content = file_get_contents($paths->getRoot() . DIRECTORY_SEPARATOR . 'composer.json');
            $content = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            $namespace = key($content['autoload']['psr-4']);
        } catch (ServiceNotFoundException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }

        $modelClass = $namespace . 'models\\' . str_replace('/', '\\', $this->modelName);

        if (!class_exists($modelClass)){
            throw new RuntimeException('model ' . $this->modelName . ' not found', 404);
        }

        $this->model = new $modelClass($this->services, array_merge($this->passedParameters, $this->bodyParameters), $verb, $this->file);

        if ($this->model->redirect() !== ''){
            $this->initialiseModel($this->model->redirect());
        }
    }

    /**
     * @return Response
     */
    abstract public function render(): Response;

    /**
     * @param int $code
     * @param string $response
     * @throws JsonException
     */
    protected function completeRender(int $code=null, string $response=null): void {
        setcookie('minimalismServices', $this->services->serialiseCookies(), time() + (30 * 24 * 60 * 60));

        $this->services->cleanNonPersistentVariables();
        $_SESSION['minimalismServices'] = $this->services;
        $this->logger->addSystemEvent(null, 'Session persisted');

        $this->model->postRender($code, $response);
    }

    /**
     * @return string
     */
    protected function getHttpType(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}