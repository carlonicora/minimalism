<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Events\MinimalismInfoEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\SecurityInterface;
use Exception;

abstract class AbstractController implements ControllerInterface
{
    /** @var string */
    protected string $modelName = 'Index';

    /** @var ServicesFactory */
    protected ServicesFactory $services;

    /** @var SecurityInterface|null  */
    protected ?SecurityInterface $security=null;

    /** @var EncrypterInterface|null  */
    protected ?EncrypterInterface $encrypter=null;

    /** @var ModelInterface */
    protected ModelInterface $model;

    /** @var array */
    protected array $passedParameters = [];

    /** @var array */
    protected ?array $file=null;

    /** @var string */
    public string $version;

    /** @var array  */
    protected array $bodyParameters = [];

    /** @var string|null  */
    protected ?string $phpInput=null;

    /** @var string  */
    protected string $verb='GET';

    /**
     * abstractController constructor.
     * @param ServicesFactory $services
     */
    public function __construct(ServicesFactory $services)
    {
        $this->services = $services;
    }

    /**
     * @param array $parameterValueList
     * @param array $parameterValues
     * @return ControllerInterface
     * @throws Exception
     */
    public function initialiseParameters(array $parameterValueList=[], array $parameterValues=[]): ControllerInterface
    {
        if (!empty($parameterValueList) || !empty($parameterValues)) {
            $this->passedParameters = $parameterValueList;
            $this->bodyParameters = $parameterValues;
        } else {
            $this->parseUriParameters();
            $this->getPhpInputParameters();

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
                    if (!empty($this->phpInput)) {
                        try {
                            $this->bodyParameters = json_decode($this->phpInput, true, 512, JSON_THROW_ON_ERROR);
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

        $this->services->logger()->info()->log(MinimalismInfoEvents::PARAMETERS_INITIALISED());

        return $this;
    }

    /**
     * @param ModelInterface|string|null $modelName
     * @param string $verb
     * @return ControllerInterface
     * @throws Exception
     */
    public function initialiseModel($modelName = null, string $verb='GET'): ControllerInterface
    {
        if ($modelName instanceof ModelInterface) {
            $this->model = $modelName;
        } else {
            if (isset($modelName)) {
                $this->modelName = str_replace('-', '\\', $modelName);
            }

            $modelClass = $this->findModelClass($this->modelName);
            $this->model = new $modelClass($this->services);
        }

        $this->model->setVerb($verb);
        $this->model->setEncrypter($this->encrypter);

        $this->model->initialise(array_merge($this->passedParameters, $this->bodyParameters), $this->file);

        if ($this->model->redirect() !== ''){
            $this->initialiseModel($this->model->redirect());
        }

        $this->services->logger()->info()->log(MinimalismInfoEvents::MODEL_INITIALISED($this->modelName));

        return $this;
    }

    /**
     * @param string $modelName
     * @return string
     * @throws Exception
     */
    private function findModelClass(string $modelName): string
    {
        $response = $this->services->paths()->getNamespace() . 'Models\\' . str_replace('/', '\\', $modelName);

        if (class_exists($response)){
            return $response;
        }

        foreach ($this->services->paths()->getServicesNamespaces() as $namespace){
            $response = $namespace . '\\Models\\' . str_replace('/', '\\', $modelName);
            if (class_exists($response)){
                return $response;
            }
        }

        $this->services->logger()->error()->log(
            MinimalismErrorEvents::MODEL_NOT_FOUND(strtolower($modelName))
        )->throw(Exception::class, null);

        return '';
    }

    /**
     *
     */
    protected function getPhpInputParameters(): void
    {
        $this->phpInput = file_get_contents('php://input');
    }

    /**
     *
     * @throws Exception
     */
    protected function parseUriParameters(): void
    {
        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $uri = strtok($_SERVER['REQUEST_URI'], '?');

            if (!(isset($uri) && $uri === '/')) {
                $variables = array_filter(explode('/', substr($uri, 1)), 'strlen');

                $this->passedParameters = $this->parseModelNameFromUri($variables);
            }
        }
    }

    /**
     * @param array $uriVariables
     * @return array
     * @throws Exception
     */
    protected function parseModelNameFromUri(array $uriVariables): array
    {
        $response = [];

        $firstArgument = current($uriVariables);
        if (false === $firstArgument || is_numeric($firstArgument)) {
            return $uriVariables;
        }

        $bestMatchDepth = 0;

        foreach ($this->services->paths()->getServicesModelsDirectories() as $modelsDirectory) {
            $currentMatchDepth = 0;
            $currentDepth = 0;
            $uriVariablesForCurrentService = $uriVariables;
            $currentResponse = [];
            $modelNameForService = '';
            $classPath = $modelsDirectory;
            foreach ($uriVariablesForCurrentService as $uriParam) {
                $currentDepth++;
                $candidate = $classPath . DIRECTORY_SEPARATOR . ucfirst($uriParam);

                if (is_file($candidate . '.php')){
                    $modelNameForService .= (!empty($modelNameForService) ? '/' : '') .ucfirst($uriParam);
                    $currentMatchDepth = $currentDepth;
                    $classPath = $candidate;
                } elseif (is_dir($candidate)) {
                    $modelNameForService .= (!empty($modelNameForService) ? DIRECTORY_SEPARATOR : '') . ucfirst($uriParam);
                    $classPath = $candidate;
                } else {
                    if ($currentDepth === 1){
                        break;
                    }
                    $currentResponse[] = $uriVariablesForCurrentService[0];
                }

                array_shift($uriVariablesForCurrentService);
            }

            if ($currentMatchDepth > $bestMatchDepth){
                $this->modelName = $modelNameForService;
                $response = $currentResponse;
            }
        }

        return $response;
    }


    /**
     * @return Response
     */
    abstract public function render(): ResponseInterface;

    /**
     * @param int $code
     * @param string $response
     */
    public function completeRender(int $code=null, string $response=null): void
    {
        $this->services->cleanNonPersistentVariables();
        $this->services->destroyStatics();
    }

    /**
     * @return string
     */
    protected function getHttpType(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * @return ControllerInterface
     */
    abstract public function postInitialise(): ControllerInterface;

    /**
     * @param SecurityInterface|null $security
     */
    public function setSecurityInterface(?SecurityInterface $security): void
    {
        $this->security = $security;
    }

    /**
     * @param EncrypterInterface|null $encrypter
     */
    public function setEncrypterInterface(?EncrypterInterface $encrypter): void
    {
        $this->encrypter = $encrypter;
    }
}