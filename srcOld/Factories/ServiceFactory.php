<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Interfaces\BuilderInterface;
use CarloNicora\Minimalism\Interfaces\CacheInterface;
use CarloNicora\Minimalism\Interfaces\DataInterface;
use CarloNicora\Minimalism\Interfaces\DataLoaderInterface;
use CarloNicora\Minimalism\Interfaces\DefaultServiceInterface;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
use CarloNicora\Minimalism\Interfaces\UserServiceInterface;
use CarloNicora\Minimalism\Services\MinimalismLogger;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Services\Pools;
use Dotenv\Dotenv;
use Dotenv\Exception\ValidationException;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;
use RuntimeException;
use Throwable;

class ServiceFactory
{
    /** @var array|ServiceInterface[]  */
    private array $services = [];

    /** @var Dotenv|null  */
    private ?Dotenv $env=null;

    /** @var string  */
    private string $servicesCacheFile;

    /** @var bool  */
    private bool $hasBeenUpdated=false;

    /**
     * @throws Exception
     */
    public function initialise(
        bool $requiresBaseService=true
    ): void
    {

        $this->servicesCacheFile = dirname(__DIR__, 5)
            . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR . 'services.cache';

        $this->loadServicesFromCache();

        if (array_key_exists(Path::class, $this->services)) {
            $this->services[Path::class]->initialise();
        } else {
            $this->services[Path::class] = new Path();
        }

        if (array_key_exists(LoggerInterface::class, $this->services)) {
            $this->services[LoggerInterface::class]->initialise();
        } else {
            $this->initialiseLogger();
        }

        MinimalismObjectsFactory::initialise(
            serviceFactory: $this
        );

        $this->initialiseCoreServices();

        if ($requiresBaseService) {
            $this->initialiseBaseService();
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        if (array_key_exists(DefaultServiceInterface::class, $this->services)) {
            /** @var DefaultServiceInterface $defaultService */
            $defaultService = $this->services[$this->services[DefaultServiceInterface::class]];

            foreach ($defaultService->getDelayedServices() ?? [] as $delayedServices) {
                if (array_key_exists($delayedServices, $this->services)) {
                    $this->services[$delayedServices]->destroy();
                }
            }
        }

        if (array_key_exists(LoggerInterface::class, $this->services)) {
            $this->services[LoggerInterface::class]->destroy();
        }

        /** @var ServiceInterface $service */
        foreach ($this->services ?? [] as $service){
            if ($service !== null
                && !is_string($service)
            ) {
                $service->destroy();
            }
        }

        if ($this->hasBeenUpdated) {
            file_put_contents($this->servicesCacheFile, serialize($this->services));
        }

        MinimalismObjectsFactory::terminate();

        session_write_close();
    }

    /**
     * @return Path
     */
    public function getPath(): Path
    {
        return $this->services[Path::class];
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->services[LoggerInterface::class];
    }

    /**
     * @throws Exception
     */
    public function initialiseLogger(): void
    {
        $namespace = $this->getDefaultNamespace();

        $loggerClass = $namespace . 'Services\\Logger\\Logger';

        if (!class_exists($loggerClass)){
            $loggerClass = MinimalismLogger::class;
        }

        $logger = $this->create($loggerClass);

        if ($logger === null){
            throw new RuntimeException('Cannot initialise the logger', 500);
        }

        unset($this->services[$loggerClass]);
        $this->services[LoggerInterface::class] = $logger;
    }

    /**
     * @return EncrypterInterface|null
     */
    public function getEncrypter(): ?EncrypterInterface
    {
        return $this->services[$this->services[EncrypterInterface::class]] ?? null;
    }

    /**
     * @return TransformerInterface|null
     */
    public function getTransformer(): ?TransformerInterface
    {
        return $this->services[$this->services[TransformerInterface::class]] ?? null;
    }

    /**
     * @throws Exception
     */
    private function initialiseBaseService(): void
    {
        if (array_key_exists(DefaultServiceInterface::class, $this->services)){
            return;
        }

        $namespace = $this->getDefaultNamespace();
        $servicePath = explode('\\', $namespace);
        $serviceName = $namespace . $servicePath[count($servicePath)-2];

        $reflectedClass = new ReflectionClass($serviceName);
        if (!$reflectedClass->implementsInterface(DefaultServiceInterface::class)){
            throw new RuntimeException('The main service is not defined as Default Service', 500);
        }

        $this->services[$serviceName] = $this->create($serviceName);
        $this->services[DefaultServiceInterface::class] = $serviceName;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getDefaultNamespace(): string
    {
        $file = json_decode(file_get_contents($this->getPath()->getRoot() . DIRECTORY_SEPARATOR . 'composer.json'), true, 512, JSON_THROW_ON_ERROR);
        if (array_key_exists('autoload', $file) && array_key_exists('psr-4', $file['autoload'])) {
            foreach ($file['autoload']['psr-4'] as $namespace => $folder) {
                if ($folder === 'src/') {
                    return $namespace;
                }
            }
        }

        throw new RuntimeException('Default namespace not found in composer.json', 500);
    }

    /**
     * @throws Exception
     */
    private function initialiseCoreServices(): void
    {
        if (! array_diff(
            [
                EncrypterInterface::class,
                TransformerInterface::class,
                DataInterface::class,
                CacheInterface::class,
                BuilderInterface::class,
                UserServiceInterface::class
            ],
            array_keys($this->services))
        ) {
            return;
        }

        $vendorServicesFiles     = glob(pattern: $this->getPath()->getRoot() . '/vendor/*/minimalism-service-*/src/*.php', flags: GLOB_NOSORT);
        $minimalismServicesFiles = glob(pattern: $this->getPath()->getRoot() . '/vendor/carlonicora/minimalism/src/Services/*.php', flags: GLOB_NOSORT);
        $internalServicesFiles   = glob(pattern: $this->getPath()->getRoot() . '/src/Services/*/*.php', flags: GLOB_NOSORT);

        $allServicesFiles = array_merge($minimalismServicesFiles, $vendorServicesFiles, $internalServicesFiles);

        $services = [];

        foreach ($allServicesFiles ?? [] as $serviceFile) {
            $serviceName = pathinfo($serviceFile, flags: PATHINFO_FILENAME);

            $code = file_get_contents($serviceFile);

            $pattern = '#^namespace\s+(.+?);$#sm';
            if (! preg_match($pattern, $code, matches: $m)) {
                $normalisedCode = preg_replace(pattern: '#(*BSR_ANYCRLF)\R#', replacement: "\n", subject: $code);
                preg_match($pattern, $normalisedCode, matches: $m);
            }

            $namespace = $m[1] ?? '';

            try {
                $serviceClass = $namespace . '\\' . $serviceName;
                $services[] = new ReflectionClass($serviceClass);
            } catch (Throwable $exception) {
                $this->getLogger()->error(
                    message: 'Minimalism failed to proccess a service "' . $serviceClass . '"',
                    context: [
                        'message' => $exception->getMessage(),
                        'file' => $exception->getFile() ?? '',
                        'line' => $exception->getLine(),
                        'exception' => $exception->getTrace(),
                    ]
                );
            }
        }

        $this->searchInterface($services, interfaceName: CacheInterface::class);
        $this->searchInterface($services, interfaceName: EncrypterInterface::class);
        $this->searchInterface($services, interfaceName: TransformerInterface::class);
        $this->searchInterface($services, interfaceName: DataInterface::class);
        $this->searchInterface($services, interfaceName: BuilderInterface::class);
        $this->searchInterface($services, interfaceName: UserServiceInterface::class);
    }

    /**
     * @param array $services
     * @param string $interfaceName
     * @throws Exception
     */
    public function searchInterface(array $services, string $interfaceName): void
    {
        foreach ($services ?? [] as $service){
            try {
                if (!array_key_exists($interfaceName, $this->services)  && $service->implementsInterface($interfaceName)) {
                    $this->services[$service->getName()] = $this->create($service->getName());
                    $this->services[$interfaceName] = $service->getName();
                    return;
                }
            } catch (ReflectionException) {
            }
        }

        $this->services[$interfaceName] = null;
    }

    /**
     *
     */
    private function loadServicesFromCache(): void
    {
        if (is_file($this->servicesCacheFile)) {
            $serviceFile = file_get_contents($this->servicesCacheFile);

            if ($serviceFile !== false) {
                $this->services = unserialize($serviceFile, [true]);

                foreach ($this->services ?? [] as $service) {
                    if ($service !== null && !is_string($service)) {
                        $service->initialise();
                    }
                }
            }
        }
    }

    /**
     * @param string $serviceName
     * @return ServiceInterface|null
     * @throws Exception
     */
    public function create(string $serviceName): ?ServiceInterface
    {
        if (!array_key_exists($serviceName, $this->services)) {
            $this->hasBeenUpdated = true;
            $parameters = $this->loadDependencies($serviceName);

            $this->services[$serviceName] = new $serviceName(...$parameters);
        } elseif (is_string($this->services[$serviceName])){
            $serviceName = $this->services[$serviceName];
        }

        return $this->services[$serviceName] ?? null;
    }

    /**
     * @param string $serviceName
     * @return array
     * @throws Exception
     */
    private function loadDependencies(string $serviceName): array
    {
        $response = [];

        try {
            $serviceReflection = new ReflectionClass($serviceName);
            if ($serviceReflection->hasMethod('__construct')) {
                $serviceParameters = $serviceReflection->getMethod('__construct')->getParameters();
                foreach ($serviceParameters ?? [] as $serviceParameter) {
                    /** @var ReflectionNamedType|ReflectionUnionType $parameter */
                    $parameter = $serviceParameter->getType();
                    try {
                        if (get_class($parameter) === ReflectionUnionType::class){
                            $subResponse = null;
                            /** @var ReflectionNamedType $subParameter */
                            foreach ($parameter->getTypes() ?? [] as $subParameter) {
                                $reflect = new ReflectionClass($subParameter->getName());
                                if ($reflect->implementsInterface(DefaultServiceInterface::class)) {
                                    $subResponse = $this->services[$this->services[DefaultServiceInterface::class]];
                                    break;
                                }

                                if ($reflect->implementsInterface(LoggerInterface::class)) {
                                    $subResponse = $this->services[LoggerInterface::class];
                                    break;
                                }
                            }
                            $response[] = $subResponse;
                        } else {
                            $reflect = new ReflectionClass($parameter->getName());
                            if ($reflect->implementsInterface(ServiceInterface::class) && $reflect->implementsInterface(EncrypterInterface::class)) {
                                if (array_key_exists(EncrypterInterface::class, $this->services)){
                                    $response[] = $this->services[$this->services[EncrypterInterface::class]];
                                } else {
                                    $response[] = null;
                                }
                            } elseif ($reflect->implementsInterface(ServiceInterface::class) && $reflect->implementsInterface(DataInterface::class)) {
                                if (array_key_exists(DataInterface::class, $this->services)) {
                                    $response[] = $this->services[$this->services[DataInterface::class]];
                                } else {
                                    $response[] = null;
                                }
                            } elseif ($reflect->implementsInterface(ServiceInterface::class) && $reflect->implementsInterface(TransformerInterface::class)) {
                                if (array_key_exists(TransformerInterface::class, $this->services)){
                                    $response[] = $this->services[$this->services[TransformerInterface::class]];
                                } else {
                                    $response[] = null;
                                }
                            } elseif ($reflect->implementsInterface(CacheInterface::class)) {
                                if (array_key_exists(CacheInterface::class, $this->services) && array_key_exists($this->services[CacheInterface::class], $this->services)) {
                                    $response[] = $this->services[$this->services[CacheInterface::class]];
                                } else {
                                    $response[] = null;
                                }
                            } elseif ($reflect->implementsInterface(ServiceInterface::class) && $reflect->implementsInterface(BuilderInterface::class)) {
                                if (array_key_exists(BuilderInterface::class, $this->services)) {
                                    $response[] = $this->services[$this->services[BuilderInterface::class]];
                                } else {
                                    $response[] = null;
                                }
                            } elseif ($reflect->implementsInterface(ServiceInterface::class) && $reflect->implementsInterface(UserServiceInterface::class)) {
                                if (array_key_exists(UserServiceInterface::class, $this->services)){
                                    $response[] = $this->services[$this->services[UserServiceInterface::class]];
                                } else {
                                    $response[] = null;
                                }
                            } elseif ($reflect->implementsInterface(LoggerInterface::class)) {
                                if (array_key_exists(LoggerInterface::class, $this->services)) {
                                    $response[] = $this->services[LoggerInterface::class];
                                } else {
                                    $response[] = null;
                                }
                            } elseif ($reflect->implementsInterface(DataLoaderInterface::class)){
                                /** @var Pools $pools */
                                $pools = $this->create(Pools::class);
                                $response[] = $pools->get($parameter->getName());
                            } elseif ($reflect->implementsInterface(ServiceInterface::class)) {
                                $response[] = $this->create($parameter->getName());
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

                            $response[] = match ($namedType->getName()) {
                                'int' => (int)$environment,
                                'bool' => filter_var($environment, FILTER_VALIDATE_BOOLEAN),
                                default => $environment,
                            };
                        } else {
                            $response[] = $environment;
                        }
                    }
                }
            }
        } catch (ReflectionException) {
            throw new RuntimeException('Service dependecies loading failed for ' . $serviceName, 500);
        }

        return $response;
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
                $this->env->load();
            } catch (Exception) {
            }
        }
    }
}