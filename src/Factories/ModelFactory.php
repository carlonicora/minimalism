<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Abstracts\AbstractFactory;
use CarloNicora\Minimalism\Builders\ModelBuilder;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use Exception;
use JsonException;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;

class ModelFactory extends AbstractFactory
{
    /** @var array  */
    private array $models;

    /** @var array  */
    private array $modelsDefinitions;

    /** @var string|null  */
    private ?string $modelClass=null;

    /**
     * @param MinimalismFactories $minimalismFactories
     * @throws Exception
     */
    public function __construct(
        MinimalismFactories $minimalismFactories,
    )
    {
        parent::__construct(minimalismFactories: $minimalismFactories);

        if (is_file($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('models.cache'))
            && ($modelsFile = file_get_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('models.cache'))) !== false
            && ($serviceModelFile = file_get_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('servicesModels.cache'))) !== false
            && ($modelDefinitionsFile = file_get_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('modelsDefinitions.cache'))) !== false
        ){
            $this->models = unserialize($modelsFile, [true]);
            $serviceModels = unserialize($serviceModelFile, [true]);
            $this->minimalismFactories->getServiceFactory()->getPath()->setServicesModels($serviceModels);
            $this->modelsDefinitions = unserialize($modelDefinitionsFile, [true]);
        } else {
            $this->modelsDefinitions = [];
            $serviceModels = [];

            $this->models = $this->loadFolderModels($this->minimalismFactories->getServiceFactory()->getPath()->getRoot()
                . DIRECTORY_SEPARATOR . 'src'
                . DIRECTORY_SEPARATOR . 'Models'
            );

            foreach ($this->minimalismFactories->getServiceFactory()->getPath()->getServicesModelsDirectories() ?? [] as $additionalDirectory) {
                $serviceModels[] = $this->loadFolderModels($additionalDirectory);
            }
            $this->minimalismFactories->getServiceFactory()->getPath()->setServicesModels($serviceModels);

            file_put_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('models.cache'), serialize($this->models));
            file_put_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('servicesModels.cache'), serialize($serviceModels));
            file_put_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('modelsDefinitions.cache'), serialize($this->modelsDefinitions));
        }
    }

    /**
     * @param string|null $modelName
     * @param array|null $parameters
     * @param string|null $function
     * @return ModelInterface
     * @throws Exception
     */
    public function create(
        ?string $modelName=null,
        ?array $parameters=null,
        ?string $function=null
    ): ModelInterface
    {
        if ($parameters === null) {
            $parameters = $this->createParameters($modelName);
        }

        /** @var ModelInterface $response */
        $response = new $modelName(
            minimalismFactories: $this->minimalismFactories,
            function: $function
        );

        $response->setParameters($parameters);

        return $response;
    }

    /**
     * @param string|null $model
     * @return array
     * @throws Exception
     */
    private function createParameters(
        ?string &$model
    ): array
    {
        if ($this->minimalismFactories->getServiceFactory()->getPath()->getUrl() === null){
            $response = $this->getCliParameters();
        } else {
            $response = $this->getWebParameters();
        }

        $model = $model??$this->modelClass??throw new RuntimeException('Model not found', 404);

        return $response;
    }

    /**
     * @param string $folder
     * @param bool $isRoot
     * @return array
     * @throws Exception
     */
    private function loadFolderModels(
        string $folder,
        bool $isRoot=true,
    ): array
    {
        $response = [];
        $models = glob($folder . DIRECTORY_SEPARATOR . '*', GLOB_NOSORT);
        foreach ($models ?? [] as $model) {
            $modelInfo = pathinfo($model);
            if (!array_key_exists('extension', $modelInfo)){
                $response[strtolower(basename($model)) . '-folder'] = $this->loadFolderModels($model, false);
            } elseif ($modelInfo['extension'] === 'php'){
                $modelName = basename(substr($model, 0, -4));

                if (preg_match('#^namespace\s+(.+?);$#sm', file_get_contents($model), $m)
                    || preg_match('#^namespace\s+(.+?);\r\n#sm', file_get_contents($model), $m)
                ) {
                    $modelClass = $m[1] . '\\' . $modelName;
                    $response[strtolower($modelName)] = $modelClass;
                    $this->initialiseModelDefinition(
                        modelClassName: $modelClass,
                    );
                }
            }
        }

        if ($isRoot && array_key_exists('index', $response)){
            $response['*'] = $response['index'];
        }

        return $response;
    }

    /**
     * @param string $modelClassName
     * @throws Exception
     */
    private function initialiseModelDefinition(
        string $modelClassName
    ): void
    {
        $response = [];

        foreach ((new ReflectionClass($modelClassName))->getMethods(ReflectionMethod::IS_PUBLIC) as $method){
            $response[$method->getName()] = $this->getMethodParametersDefinition($method);
        }

        $this->modelsDefinitions[$modelClassName] = $response;
    }

    /**
     * @param string $modelName
     * @param string $functionName
     * @return array
     */
    public function getModelMethodParametersDefinition(
        string $modelName,
        string $functionName,
    ): array
    {
        return $this->modelsDefinitions[$modelName][$functionName]??throw new RuntimeException('no', 500);
    }

    /**
     * @return array
     */
    public function getCliParameters(): array
    {
        $response = [
            'named' => []
        ];

        $typeName = null;
        foreach ($_SERVER['argv'] ?? [] as $item) {
            if (str_starts_with($item, '-')){
                while (str_starts_with($item, '-')){
                    $item = substr($item, 1);
                }
                $typeName = $item;
            } elseif ($typeName !== null) {
                $response['named'][$typeName] = $item;
                $typeName = null;
            } else {
                try {
                    $response['named']['payload'] = json_decode($item, true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException) {
                }
            }
        }

        return $response;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getWebParameters(): array
    {
        $response = [];
        [$uri, $namedParametersString] = array_pad(
            explode('?', $_SERVER['REQUEST_URI'] ?? ''),
            2,
            ''
        );

        /** @noinspection UnusedFunctionResultInspection */
        $this->minimalismFactories->getServiceFactory()->getPath()->sanitiseUriVersion($uri);

        if ($uri === '/'){
            $this->modelClass = $this->models['*'];
        } else {
            $uriParts = explode('/', substr($uri, 1));
            $modelBuilder = new ModelBuilder($uriParts, $this->models, $this->minimalismFactories->getServiceFactory()->getPath()->getServicesModels());

            $this->modelClass = $modelBuilder->getModelClass();
            $response['positioned'] = $modelBuilder->getParameters();

            unset($modelBuilder);
        }

        $response['named'] = $this->getNamedParameters($namedParametersString);

        return $response;
    }

    /**
     * @param string|null $namedParametersString
     * @return array
     */
    private function getNamedParameters(
        ?string $namedParametersString
    ): array
    {
        $response = [];
        if ($namedParametersString !== null && $namedParametersString !== '') {
            $namedParameters = explode('&', $namedParametersString);
            foreach ($namedParameters ?? [] as $namedParameter) {
                [$parameterName, $parameterValue] = explode('=', $namedParameter);
                $response[$parameterName] = $parameterValue;
            }
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET'){
            foreach ($_GET as $parameter => $value) {
                $response[$parameter] = $value;
            }
        } else {
            if (!empty($phpInput=file_get_contents('php://input'))) {
                try {
                    $response['payload'] = json_decode($phpInput, true, 512, JSON_THROW_ON_ERROR);
                } catch (Exception) {
                    try {
                        $additionalResponse = [];
                        parse_str($phpInput, $additionalResponse);

                        if ($additionalResponse !== []) {
                            $response = array_merge($response, $additionalResponse);
                        }
                    } catch (Exception) {
                    }
                }
            }

            $response['files'] = $this->reArrayFiles($_FILES);

            foreach ($_POST as $parameter => $value) {
                $response[$parameter] = $value;
            }
        }

        return $response;
    }

    /**
     * @param array $files
     * @return array
     */
    private function reArrayFiles(
        array $files,
    ): array
    {
        $result = [];
        if (empty($files)) {
            return $result;
        }

        foreach ($files as $key => $file) {
            if (is_string($file['name'])) {
                $result[$key] = $file;
            } elseif (is_array($file['name'])) {
                $result[$key] = [];
                foreach ($file as $lastKey => $value1) {
                    $result[$key] = array_replace_recursive($result[$key], $this->recursive($lastKey, $value1));
                }
            }

        }

        return $result;
    }

    /**
     * @param $lastKey
     * @param $input
     * @return array
     */
    private function recursive(
        $lastKey,
        $input,
    ): array
    {
        $result = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->recursive($lastKey, $value);
            } else {
                $result[$key][$lastKey] = $value;
            }
        }

        return $result;
    }
}