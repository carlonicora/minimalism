<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Abstracts\AbstractFactory;
use CarloNicora\Minimalism\Builders\ModelBuilder;
use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Objects\ModelParameters;
use Exception;
use JsonException;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Throwable;

class ModelFactory extends AbstractFactory
{
    /** @var array  */
    private array $models;

    /** @var array  */
    private array $modelsDefinitions;

    /** @var string|null  */
    private ?string $modelClass=null;

    /**
     * @throws Exception
     */
    public function initialiseFactory(
    ): void
    {
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

            foreach ($this->minimalismFactories->getServiceFactory()->getPath()->getServicesModelsDirectories() as $additionalDirectory) {
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
     * @param ModelParameters|null $parameters
     * @param string|null $function
     * @return ModelInterface
     * @throws Exception
     */
    public function create(
        ?string $modelName=null,
        ?ModelParameters $parameters=null,
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
     * @return ModelParameters
     * @throws Exception
     */
    protected function createParameters(
        ?string &$model
    ): ModelParameters
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
    protected function loadFolderModels(
        string $folder,
        bool $isRoot=true,
    ): array
    {
        $response = [];
        foreach (glob($folder . DIRECTORY_SEPARATOR . '*', GLOB_NOSORT) as $model) {
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
            try {
                $response[$method->getName()] = $this->getMethodParametersDefinition($method);
            } catch (Exception|Throwable) {
            }
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
        if (!array_key_exists($modelName, $this->modelsDefinitions)){
            throw new RuntimeException('Model not found', HttpCode::NotFound->value);
        }

        if (!array_key_exists(strtolower($functionName), $this->modelsDefinitions[$modelName])){
            throw new RuntimeException('Method ' . $functionName . ' not implemented', HttpCode::NotImplemented->value);
        }

        return $this->modelsDefinitions[$modelName][strtolower($functionName)];
    }

    /**
     * @return ModelParameters
     */
    public function getCliParameters(
    ): ModelParameters
    {
        $response = new ModelParameters();

        $typeName = null;
        foreach ($_SERVER['argv'] ?? [] as $item) {
            if (str_starts_with($item, '-')){
                while (str_starts_with($item, '-')){
                    $item = substr($item, 1);
                }
                $typeName = $item;
            } elseif ($typeName !== null) {
                $response->addNamedParameter($typeName, $item);
                $typeName = null;
            } else {
                try {
                    $response->addNamedParameter('payload', json_decode($item, true, 512, JSON_THROW_ON_ERROR));
                } catch (JsonException) {
                }
            }
        }

        return $response;
    }

    /**
     * @return ModelParameters
     * @throws Exception
     */
    public function getWebParameters(
    ): ModelParameters
    {
        $response = new ModelParameters();

        [$uri, $namedParametersString] = array_pad(
            explode('?', $_SERVER['REQUEST_URI'] ?? ''),
            2,
            ''
        );

        /** @noinspection UnusedFunctionResultInspection */
        $this->minimalismFactories->getServiceFactory()->getPath()->sanitiseUriVersion($uri);

        if (strlen($uri) > 1 && str_ends_with(haystack: $uri, needle: '/')){
            $uri = substr($uri, 0, -1);
        }

        if ($uri === '/'){
            $this->modelClass = $this->models['*'];
        } else {
            $uriParts = explode('/', substr($uri, 1));
            $modelBuilder = new ModelBuilder($uriParts, $this->models, $this->minimalismFactories->getServiceFactory()->getPath()->getServicesModels());

            $this->modelClass = $modelBuilder->getModelClass();

            foreach ($modelBuilder->getParameters() as $value){
                $response->addPositionedParameter($value);
            }

            unset($modelBuilder);
        }

        $this->setNamedParameters($response, $namedParametersString);

        return $response;
    }

    /**
     * @param ModelParameters $modelParameters
     * @param string|null $namedParametersString
     * @throws Exception
     */
    private function setNamedParameters(
        ModelParameters $modelParameters,
        ?string $namedParametersString,
    ): void
    {
        if ($namedParametersString !== null && $namedParametersString !== '') {
            foreach (explode('&', $namedParametersString) as $namedParameter) {
                [$parameterName, $parameterValue] = explode('=', $namedParameter);
                $modelParameters->addNamedParameter($parameterName, $parameterValue);
            }
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET'){
            foreach ($_GET as $parameter => $value) {
                $modelParameters->addNamedParameter($parameter, $value);
            }
        } else {
            if (!empty($phpInput=file_get_contents('php://input'))) {
                try {
                    $modelParameters->addNamedParameter('payload', json_decode($phpInput, true, 512, JSON_THROW_ON_ERROR));
                } catch (Exception) {
                    try {
                        $additionalResponse = [];
                        parse_str($phpInput, $additionalResponse);

                        foreach ($additionalResponse ?? [] as $parameterName=>$parameterValue){
                            if ($parameter === 'payload'){
                                $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                            }
                            $modelParameters->addNamedParameter($parameterName, $parameterValue);
                        }
                    } catch (Exception) {
                    }
                }
            }

            foreach ($_POST as $parameter => $value) {
                if ($parameter === 'payload'){
                    $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                }
                $modelParameters->addNamedParameter($parameter, $value);
            }

            $this->setFiles($modelParameters, $_FILES);
        }
    }

    /**
     * @param ModelParameters $modelParameters
     * @param array $files
     */
    private function setFiles(
        ModelParameters $modelParameters,
        array $files,
    ): void
    {
        if (empty($files)) {
            return;
        }

        foreach ($files as $key => $file) {
            if (is_string($file['name'])) {
                $modelParameters->addFile($key, $file);
            } elseif (is_array($file['name'])) {
                $recursiveFile = [];
                foreach ($file as $lastKey => $value1) {
                    $recursiveFile = array_replace_recursive($recursiveFile, $this->recursive($lastKey, $value1));
                }
                $modelParameters->addFile($key, $recursiveFile);
            }

        }
    }

    /**
     * @param mixed $lastKey
     * @param array $input
     * @return array
     */
    private function recursive(
        mixed $lastKey,
        array $input,
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