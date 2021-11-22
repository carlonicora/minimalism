<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Interfaces\DefaultServiceInterface;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Interfaces\ObjectFactoryInterface;
use CarloNicora\Minimalism\Interfaces\ObjectInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
use CarloNicora\Minimalism\Services\Path;
use Dotenv\Dotenv;
use Dotenv\Exception\ValidationException;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;
use RuntimeException;

class MinimalismFactory
{
    /** @var ServiceInterface[]|string[]|Path[]  */
    private array $services=[];

    /** @var array  */
    private array $modelsDefinitions;

    /** @var string  */
    private string $servicesCacheFile;

    /** @var bool  */
    private bool $hasServiceListBeenUpdated=false;

    /** @var Dotenv|null  */
    private ?Dotenv $env=null;

    /**
     *
     */
    public function __construct(
    )
    {
        $this->services[Path::class] = new Path();

        $cacheFolder = $this->services[Path::class]->getRoot() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
        $this->servicesCacheFile = $cacheFolder . 'services.cache';

        $this->intialiseServices();
        $this->loadModelsDefinition();
    }

    /**
     *
     */
    public function __destruct()
    {
        if ($this->hasServiceListBeenUpdated) {
            file_put_contents($this->servicesCacheFile, serialize($this->services));
        }
    }

    /**
     * @return Path
     */
    public function getPath(
    ): Path
    {
        return $this->services[Path::class];
    }

    /**
     * @param string $className
     * @param array|null $parameters
     * @return ServiceInterface|ObjectInterface
     * @throws Exception
     */
    public function create(
        string $className,
        ?array $parameters=null,
    ): ServiceInterface|ObjectInterface
    {
        if (array_key_exists($className, $this->services)){
            return $this->services[$className];
        }

        try {
            $reflectionClass = new ReflectionClass($className);
        } catch (ReflectionException) {
            throw new RuntimeException('NOPE');
        }

        if ($reflectionClass->implementsInterface(ServiceInterface::class)){
            $response = $this->createService($reflectionClass);
            $this->services[$className] = $response;
        } elseif ($reflectionClass->implementsInterface(ObjectInterface::class)){
            $response = $this->createObject(
                reflectionClass: $reflectionClass,
                parameters: $parameters??[],
            );
        } else {
            throw new RuntimeException('NOPE');
        }

        return $response;
    }

    /**
     * @param string|null $modelName
     * @param array|null $parameters
     * @param string|null $function
     * @return ModelInterface
     * @throws Exception
     */
    public function createModel(
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
     * @param ReflectionClass $reflectionClass
     * @return ServiceInterface
     * @throws Exception
     */
    private function createService(
        ReflectionClass $reflectionClass,
    ): ServiceInterface
    {
        $serviceName = $reflectionClass->name;
        $parameters = [];

        try {
            $serviceParameters = $reflectionClass->getMethod('__construct')->getParameters();

            foreach ($serviceParameters ?? [] as $serviceParameter) {
                /** @var ReflectionNamedType|ReflectionUnionType $parameter */
                $parameter = $serviceParameter->getType();
                try {
                    if (get_class($parameter) === ReflectionUnionType::class){
                        /** @var ReflectionNamedType $subParameter */
                        foreach ($parameter->getTypes() ?? [] as $subParameter) {
                            $reflect = new ReflectionClass($subParameter->getName());
                            if ($reflect->implementsInterface(DefaultServiceInterface::class)) {
                                $this->services[DefaultServiceInterface::class] = $reflect->getName();
                                $parameters[] = $this->create($reflect->getName());
                                break;
                            }
                        }
                    } else {
                        $reflect = new ReflectionClass($parameter->getName());
                        if ($reflect->implementsInterface(ServiceInterface::class)) {

                            if ($reflect->implementsInterface(TransformerInterface::class)) {
                                $this->services[TransformerInterface::class] = $reflect->getName();
                            }
                            $parameters[] = $this->create($reflect->getName());
                        }
                    }
                } catch (ReflectionException) {
                    $this->loadDotEnv();

                    if (!$serviceParameter->isOptional()) {
                        try {
                            $this->env->required($serviceParameter->getName())->notEmpty();
                        } catch (ValidationException $e) {
                            throw new RuntimeException(
                                message: 'An environment variable is missing. ' . $e->getMessage(),
                                code: 500,
                                previous: $e
                            );
                        }
                        $environment = $_ENV[$serviceParameter->getName()];
                    } elseif (array_key_exists($serviceParameter->getName(), $_ENV)){
                        $environment = $_ENV[$serviceParameter->getName()];
                    } else {
                        $environment = $serviceParameter->isDefaultValueAvailable() ? $serviceParameter->getDefaultValue() : null;
                    }

                    if ($serviceParameter->hasType() && get_class($parameter) !== ReflectionUnionType::class) {
                        /** @var ReflectionNamedType $namedType */
                        $namedType = $serviceParameter->getType();

                        $parameters[] = match ($namedType->getName()) {
                            'int' => (int)$environment,
                            'bool' => filter_var($environment, FILTER_VALIDATE_BOOLEAN),
                            default => $environment,
                        };
                    } else {
                        $parameters[] = $environment;
                    }
                }
            }
        } catch (ReflectionException) {
            throw new RuntimeException('Service dependecies loading failed for ' . $serviceName, 500);
        }

        $this->services[$serviceName] = new $serviceName(...$parameters);

        return $this->services[$serviceName];
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param array $parameters
     * @return ObjectInterface
     */
    private function createObject(
        ReflectionClass $reflectionClass,
        array $parameters,
    ): ObjectInterface
    {
        $factoryName = null;

        try {
            /** @var ReflectionUnionType $types */
            $types = $reflectionClass->getMethod('getObjectFactoryClass')->getReturnType();

            foreach ($types->getTypes() as $type){
                if ($type->getName() !== 'string'){
                    $factoryName = $type->getName();
                    break;
                }
            }
        } catch (ReflectionException) {
            $factoryName = null;
        }

        if ($factoryName === null){
            throw new RuntimeException('nope');
        }

        /** @var ObjectFactoryInterface $factory */
        $factory = new $factoryName();

        return $factory->create(
            $reflectionClass->name,
            $parameters,
        );
    }

    /**
     *
     */
    private function intialiseServices(
    ):void
    {
        if (is_file($this->servicesCacheFile) && ($serviceFile = file_get_contents($this->servicesCacheFile)) !== false ) {
            $this->services = unserialize($serviceFile, [true]);

            foreach ($this->services ?? [] as $service) {
                if ($service !== null && !is_string($service)) {
                    $service->initialise();
                }
            }
        } else {
            $this->preloadService();
        }
    }

    /**
     * @throws Exception
     */
    private function preloadService(
    ): void
    {
        $vendorServicesFiles = glob(pattern: $this->getPath()->getRoot() . '/vendor/*/minimalism-service-*/src/*.php', flags: GLOB_NOSORT);
        $minimalismServicesFiles = glob(pattern: $this->getPath()->getRoot() . '/vendor/carlonicora/minimalism/src/Services/*.php', flags: GLOB_NOSORT);
        $internalServicesFiles = glob(pattern: $this->getPath()->getRoot() . '/src/Services/*/*.php', flags: GLOB_NOSORT);

        $allServicesFiles = array_merge($minimalismServicesFiles, $vendorServicesFiles, $internalServicesFiles);

        foreach ($allServicesFiles ?? [] as $serviceFile) {
            $serviceName = pathinfo($serviceFile, flags: PATHINFO_FILENAME);
            $code = file_get_contents($serviceFile);

            $pattern = '#^namespace\s+(.+?);$#sm';
            if (!preg_match($pattern, $code, matches: $m)) {
                /** @noinspection SyntaxError */
                $normalisedCode = preg_replace(pattern: '#(*BSR_ANYCRLF)\R#', replacement: "\n", subject: $code);
                preg_match($pattern, $normalisedCode, matches: $m);
            }

            $namespace = $m[1] ?? '';

            $serviceClass = $namespace . '\\' . $serviceName;
            /** @noinspection UnusedFunctionResultInspection */
            $this->create(className: $serviceClass);
        }
    }

    /**
     *
     */
    private function loadModelsDefinition(
    ): void
    {
        if ($this->modelsDefinition === null){
            //load the models definition
        }
    }

    /**
     * @throws Exception
     */
    private function loadDotEnv(): void
    {
        if ($this->env === null) {
            $names = empty($_SERVER['HTTP_TEST_ENVIRONMENT']) ? ['.env'] : ['.env.testing'];

            $this->env = Dotenv::createImmutable($this->getPath()->getRoot(), $names);
            try {
                /** @noinspection UnusedFunctionResultInspection */
                $this->env->load();
            } catch (Exception) {
            }
        }
    }
}