<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Interfaces\DataLoaderInterface;
use CarloNicora\Minimalism\Interfaces\EncryptedParameterInterface;
use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Interfaces\ParameterInterface;
use CarloNicora\Minimalism\Interfaces\PositionedParameterInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

class ModelFactory
{
    public const PARAMETER_TYPE_SERVICE=1;
    public const PARAMETER_TYPE_DOCUMENT=2;
    public const PARAMETER_TYPE_SIMPLE=3;
    public const PARAMETER_TYPE_LOADER=4;
    public const PARAMETER_TYPE_PARAMETER=5;
    public const PARAMETER_TYPE_ENCRYPTER_PARAMETER=6;

    /** @var ServiceFactory|null  */
    private ?ServiceFactory $services=null;

    /** @var array  */
    private array $models;

    /** @var array  */
    private array $modelsDefinitions;

    /**
     * @param ServiceFactory $services
     * @throws Exception
     */
    public function initialise(ServiceFactory $services): void
    {
        $this->services = $services;
        $this->loadModels();
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
        $parametersFactory = new ParametersFactory(
            $this->services,
            $this->models,
        );

        if ($parameters === null) {
            $parameters = $parametersFactory->createParameters();
        }

        if ($modelName === null){
            $modelName = $parametersFactory->getModelClass();
        }

        $modelDefinition = $this->modelsDefinitions[$modelName];

        /** @var ModelInterface $response */
        $response = new $modelName(
            services: $this->services,
            modelDefinition: $modelDefinition,
            function: $function
        );

        $response->setParameters($parameters);

        return $response;
    }

    /**
     *
     * @throws Exception
     */
    private function loadModels(): void
    {
        $modelDirectoryCache = $this->services->getPath()->getRoot()
            . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR;

        $modelCache = $modelDirectoryCache . 'models.cache';
        $serviceModelCache = $modelDirectoryCache . 'servicesModels.cache';
        $modelDefinitionsCache = $modelDirectoryCache . 'modelsDefinitions.cache';

        if (is_file($modelCache)
            && ($modelsFile = file_get_contents($modelCache)) !== false
            && ($serviceModelFile = file_get_contents($serviceModelCache)) !== false
            && ($modelDefinitionsFile = file_get_contents($modelDefinitionsCache)) !== false
        ){
            $this->models = unserialize($modelsFile, [true]);
            $serviceModels = unserialize($serviceModelFile, [true]);
            $this->services->getPath()->setServicesModels($serviceModels);
            $this->modelsDefinitions = unserialize($modelDefinitionsFile, [true]);
        } else {
            $this->modelsDefinitions = [];
            $serviceModels = [];

            $this->models = $this->loadFolderModels($this->services->getPath()->getRoot()
                . DIRECTORY_SEPARATOR . 'src'
                . DIRECTORY_SEPARATOR . 'Models'
            );

            foreach ($this->services->getPath()->getServicesModelsDirectories() ?? [] as $additionalDirectory) {
                $serviceModels[] = $this->loadFolderModels($additionalDirectory);
            }
            $this->services->getPath()->setServicesModels($serviceModels);

            file_put_contents($modelCache, serialize($this->models));
            file_put_contents($serviceModelCache, serialize($serviceModels));
            file_put_contents($modelDefinitionsCache, serialize($this->modelsDefinitions));
        }
    }

    /**
     * @param string $folder
     * @param bool $isRoot
     * @return array
     * @throws Exception
     */
    private function loadFolderModels(string $folder, bool $isRoot=true): array
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
                    $this->initialiseModel($modelClass);
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
    private function initialiseModel(
        string $modelClassName
    ): void
    {
        $response = [];

        foreach ((new ReflectionClass($modelClassName))->getMethods(ReflectionMethod::IS_PUBLIC) as $method){
            if ($method->class === $modelClassName) {
                $methodResponse = [];
                $methodParameters = $method->getParameters();

                /** @var ReflectionParameter $methodParameter */
                foreach ($methodParameters ?? [] as $methodParameter) {
                    /** @var ReflectionNamedType $parameter */
                    $parameter = $methodParameter->getType();

                    $parameterResponse = [
                        'name' => $methodParameter->getName(),
                        'allowsNull' => $methodParameter->allowsNull(),
                        'defaultValue' => $methodParameter->isDefaultValueAvailable()
                            ? $methodParameter->getDefaultValue()
                            : null
                    ];

                    try {
                        $methodParameterType = new ReflectionClass($parameter->getName());
                        if ($methodParameterType->implementsInterface(ServiceInterface::class)) {
                            $parameterResponse['type'] = self::PARAMETER_TYPE_SERVICE;
                            $parameterResponse['identifier'] = $methodParameterType->getName();
                        } elseif ($methodParameterType->implementsInterface(LoggerInterface::class)){
                            $parameterResponse['type'] = self::PARAMETER_TYPE_SERVICE;
                            $parameterResponse['identifier'] = $methodParameterType->getName();
                        } elseif ($methodParameterType->implementsInterface(EncryptedParameterInterface::class)) {
                            $parameterResponse['type'] = self::PARAMETER_TYPE_ENCRYPTER_PARAMETER;
                            if ($methodParameterType->implementsInterface(PositionedParameterInterface::class)) {
                                $parameterResponse['isPositionedParameter'] = true;
                            } else {
                                $parameterResponse['isPositionedParameter'] = false;
                            }
                            $parameterResponse['identifier'] = EncryptedParameterInterface::class;
                        } elseif ($methodParameterType->implementsInterface(ParameterInterface::class)) {
                            if ($methodParameterType->implementsInterface(PositionedParameterInterface::class)) {
                                $parameterResponse['isPositionedParameter'] = true;
                            } else {
                                $parameterResponse['isPositionedParameter'] = false;
                            }
                            $parameterResponse['type'] = self::PARAMETER_TYPE_PARAMETER;
                            $parameterResponse['identifier'] = ParameterInterface::class;
                        } elseif ($methodParameterType->implementsInterface(DataLoaderInterface::class)) {
                            $parameterResponse['type'] = self::PARAMETER_TYPE_LOADER;
                            $parameterResponse['identifier'] = $methodParameterType->getName();
                        } elseif ($parameter->getName() === Document::class) {
                            $parameterResponse['type'] = self::PARAMETER_TYPE_DOCUMENT;
                            $parameterResponse['identifier'] = Document::class;
                        }
                    } catch (ReflectionException) {
                        $parameterResponse['type'] = self::PARAMETER_TYPE_SIMPLE;
                        $parameterResponse['identifier'] = $parameter->getName();
                    }

                    $methodResponse[] = $parameterResponse;
                }

                $response[$method->getName()] = $methodResponse;
            }
        }

        $this->modelsDefinitions[$modelClassName] = $response;
    }
}