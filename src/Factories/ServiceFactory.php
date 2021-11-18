<?php

namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Interfaces\CoreInterface;
use CarloNicora\Minimalism\Interfaces\DataLoaderInterface;
use CarloNicora\Minimalism\Interfaces\DefaultServiceInterface;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\InitialisableInterface;
use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Services\Pools;
use Dotenv\Dotenv;
use Dotenv\Exception\ValidationException;
use Exception;
use JetBrains\PhpStorm\Pure;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use RuntimeException;

class ServiceFactory
{

    // TODO test how new code works with the core services
//    private const CORE_SERVICES = [
//        Path::class,
//        LoggerInterface::class,
//        EncrypterInterface::class,
//        TransformerInterface::class,
//        DataInterface::class,
//        CacheInterface::class,
//        BuilderInterface::class,
//        UserServiceInterface::class,
//    ];

    /**
     * @var array
     */
    private static array $interfaceImplementations = [];
    // self::$interfaceImplementations = [LoggerInterface::class => MinimalismLogger::class, ThirdPartyInterface::class => ThirdPartyImplementation::class]

    /**
     * @var ReflectionClass[]
     */
    private static array $sortedServicesReflections = [];
    // self::$sortedServicesReflections = [MinimalismLogger::class => $reflectionOfMinimalismLogger, ThirdPartyImplementation::class => $reflection]

    /** @var array|ServiceInterface[]|InitialisableInterface[] */
    private static array $services = [];
    // self::$services = [MinimalismLogger::class => $loggerService, ThirdPartyImplementation::class => $object // or null if not initialised yet]

    /** @var Dotenv|null */
    private static ?Dotenv $env = null;

    /** @var string */
    private static string $servicesCacheFile;

    /** @var bool */
    private static bool $hasBeenUpdated = false;

    /**
     * @throws Exception
     */
    public function initialise(
        bool $requiresBaseService = true
    ): void
    {
        if (PHP_SAPI !== 'cli') {
            self::startSession();
        }

        // loadSortedServicesReflections should be called first, otherwise any calls to create or load will fail
        self::loadSortedServicesReflections();

        // TODO implement priority and load it first
        $this->create(Path::class);

        self::$servicesCacheFile = dirname(__DIR__, 5)
            . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR . 'services.cache';

        self::loadServicesFromCache();

        foreach (self::$sortedServicesReflections as $serviceKey => $serviceReflection) {
            // Go through all existing services reflections and links

            if (! $requiresBaseService && $serviceKey === DefaultServiceInterface::class) {
                // the base service if not required
                // TODO ask Carlo, why we don't need to load the base service in tests? If because it is easier to mock with thisout it, should we look at architecture again?
                // TODO Maybe some other core services (except the BasicService) should not be loaded for tests?
                // TODO ask Carlo, minimalismObjects should be loaded before or after core services?
                continue;
            }

            // Objects which implements this interface, should be loaded and cached on each request, not waiting for the first initalisation
            // Examples - Path, Logger, Cache, MySql
            if ($serviceReflection->implementsInterface(interface: CoreInterface::class)) {
                // If it is a core service - create and initialise it
                self::$services [$serviceReflection->getName()] = self::load($serviceReflection);
            }
        }

        MinimalismObjectsFactory::initialise($this);
    }

    /**
     *
     */
    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (isset($_COOKIE['PHPSESSID'])) {
                $sessid = '';

                if (ini_get('session.use_cookies')) {
                    $sessid = $_COOKIE['PHPSESSID'];
                } elseif (! ini_get('session.use_only_cookies')) {
                    $sessid = $_GET['PHPSESSID'];
                }

                if (! preg_match('/^[a-z0-9]{32}$/', $sessid)) {
                    return;
                }
            }

            session_start();
        }
    }

    private static function loadSortedServicesReflections(): void
    {
        // TODO somehow implement order and hardcode the highest prirority to Path, LoggerInterface.
        // TODO save interface implementation links to cache
        $rootPath = dirname(path: __DIR__, levels: 5);
        if (empty(self::$sortedServicesReflections)) {
            $minimalismServicesFiles = glob(pattern: $rootPath . '/vendor/carlonicora/minimalism/src/Services/*.php', flags: GLOB_NOSORT);
            $vendorServicesFiles     = glob(pattern: $rootPath . '/vendor/*/minimalism-service-*/src/*.php', flags: GLOB_NOSORT);
            $internalServicesFiles   = glob(pattern: $rootPath . '/src/Services/*/*.php', flags: GLOB_NOSORT);
            $baseServiceFiles        = glob(pattern: $rootPath . '/src/*.php', flags: GLOB_NOSORT);

            $allServicesFiles = array_merge($minimalismServicesFiles, $vendorServicesFiles, $internalServicesFiles, $baseServiceFiles);

            foreach ($allServicesFiles ?? [] as $serviceFullFileName) {
                $serviceCode = file_get_contents($serviceFullFileName);

                $pattern = '#^namespace\s+(.+?);$#sm';
                if (! preg_match($pattern, $serviceCode, matches: $m)) {
                    $normalisedCode = preg_replace(pattern: '#(*BSR_ANYCRLF)\R#', replacement: "\n", subject: $serviceCode);
                    preg_match($pattern, $normalisedCode, matches: $m);
                }

                $namespace = $m[1] ?? '';

                $serviceClassName = $namespace . '\\' . pathinfo($serviceFullFileName, flags: PATHINFO_FILENAME);

                try {
                    $candidate = new ReflectionClass($serviceClassName);
                    if ($candidate->isInstantiable()) {
                        foreach ($candidate->getInterfaceNames() as $interfaceName) {
                            // TODO think how to protect from several implementations of one third party interface or parent interface with child interfaces
                            // For example, two child interfaces of a parent interface. If a developer set the parent Interface as a dependency
                            // The current implementation will remember the 'last' implementation of a child interface
                            if ($interfaceName !== ServiceInterface::class &&
                                $interfaceName !== InitialisableInterface::class &&
                                $interfaceName !== CoreInterface::class
                            ) {
                                self::$interfaceImplementations[$interfaceName] = $candidate->getName();
                            }
                        }

                        self::$sortedServicesReflections [$candidate->getName()] = $candidate;
                        // [StripeService => $reflection]
                        // $serviceFactory->create(StripeService)
                    }
                } catch (ReflectionException $e) {
                    throw new LogicException(message: $serviceFullFileName . ' service reflection failed', code: 500, previous: $e);
                }
            }
        }
    }

    /**
     *
     */
    private static function loadServicesFromCache(): void
    {
        if (is_file(self::$servicesCacheFile)) {
            $serviceFile = file_get_contents(self::$servicesCacheFile);

            if ($serviceFile !== false) {
                self::$services = unserialize($serviceFile, [true]);
            }
        }
    }

    /**
     * @param string $serviceName
     * @return ServiceInterface|null
     * @throws ReflectionException
     */
    public function create(string $serviceName): ?ServiceInterface
    {
        $reflection = self::getServiceReflection($serviceName);

        if ($reflection === null) {
            if (class_exists($serviceName)) {
                // TODO can/should we clear and reload cache here?
                throw new RuntimeException(message: 'Cache ' . $serviceName . ' is not cached. Clear the cache, please.', code: 500);
            }

            throw new LogicException(message: 'Service ' . $serviceName . ' is not implemented', code: 500);
        }

        return self::load($reflection);
    }

    /**
     * @param ReflectionClass $reflection
     * @return ServiceInterface|null
     * @throws ReflectionException
     * @throws Exception
     */
    private static function load(
        ReflectionClass $reflection
    ): ?ServiceInterface
    {
        $serviceName = $reflection->getName();

        if (! array_key_exists($serviceName, self::$services)) {
            self::$hasBeenUpdated = true;

            $parameters = self::loadDependencies($reflection);

            if ($reflection->implementsInterface(interface: DataLoaderInterface::class)) {
                // TODO create a separate factory for Readers and Writers
                /** @var Pools $pools */
                $pools = self::$services[Pools::class] ?? self::load(new ReflectionClass(objectOrClass: Pools::class));
                self::$services[$serviceName] = $pools->get($serviceName);
            } else {
                self::$services[$serviceName] = new $serviceName(...$parameters);
            }
        }

        if (self::$services[$serviceName] instanceof InitialisableInterface) {
            self::$services[$serviceName]->initialise();
        }

        return self::$services[$serviceName];
    }

    /**
     * @param string $serviceName
     * @return ReflectionClass|null
     */
    private static function getServiceReflection(
        string $serviceName
    ): ?ReflectionClass
    {
        if (array_key_exists(key: $serviceName, array: self::$interfaceImplementations)) {
            return self::$sortedServicesReflections[self::$interfaceImplementations[$serviceName]] ?? null;
        }

        return self::$sortedServicesReflections[$serviceName] ?? null;
    }

    /**
     * @param ReflectionClass $serviceReflection
     * @return array
     * @throws ReflectionException
     * @throws LogicException
     * @throws Exception
     */
    private static function loadDependencies(ReflectionClass $serviceReflection): array
    {
        $response = [];

        if ($serviceReflection->hasMethod(name: '__construct')) {
            $serviceConstructorParameters = $serviceReflection->getMethod(name: '__construct')->getParameters();

            foreach ($serviceConstructorParameters ?? [] as $constructorParameter) {

                $dependencyClassOrInterface = self::getDependencyClassOrInterface($constructorParameter->getType());

                if ($dependencyClassOrInterface !== null) {
                    if (array_key_exists($dependencyClassOrInterface, self::$services)) {
                        $response []= self::$services[$dependencyClassOrInterface];
                    } else {
                        $dependencyReflection = self::getServiceReflection($dependencyClassOrInterface);
                        if ($dependencyReflection) {
                            $response []= self::load($dependencyReflection);
                        } else {
                            throw new LogicException(message: 'Service ' . $dependencyClassOrInterface . ' is not implemented, or cache is not cleared', code:  500);
                        }
                    }
                } else {
                    $response [] = self::getEnvVariable($constructorParameter);
                }
            }
        }

        return $response;
    }

    /**
     * @param ReflectionType|null $dependencyType
     * @return ReflectionNamedType|null
     */
    #[Pure]
    private static function getDependencyClassOrInterface(
        ReflectionType $dependencyType = null
    ): ?string
    {
        if ($dependencyType === null) {
            return null;
        }

        if ($dependencyType instanceof ReflectionNamedType) {
            if (! $dependencyType->isBuiltin()) {
                return $dependencyType->getName();
            }

            return null;
        }

        if ($dependencyType instanceof ReflectionUnionType) {
            $subTypes = $dependencyType->getTypes();
            foreach ($subTypes as $subType) {
                if (! $subType->isBuiltin()) {
                    // TODO what if several sub types implementations exists?
                    return $subType->getName();
                }
            }
        }

        return null;
    }


    /**
     * @param ReflectionParameter $envVariable
     * @return mixed
     * @throws LogicException
     */
    private static function getEnvVariable(
        ReflectionParameter $envVariable
    ): mixed
    {
        self::loadDotEnv();

        if (! $envVariable->isOptional()) {
            try {
                self::$env->required($envVariable->getName())->notEmpty();
            } catch (ValidationException $e) {
                throw new LogicException(
                    message: 'An environment variable is missing. ' . $e->getMessage(),
                    code: 500,
                    previous: $e
                );
            }
        }

        if (array_key_exists($envVariable->getName(), $_ENV)) {
            $result = $_ENV[$envVariable->getName()];
        } else {
            $result = $envVariable->isDefaultValueAvailable() ? $envVariable->getDefaultValue() : null;
        }

        $filter = match ($envVariable->getType()?->getName()) {
            'int'   => FILTER_VALIDATE_INT,
            'bool'  => FILTER_VALIDATE_BOOL,
            default => FILTER_DEFAULT
        };

        return filter_var($result, $filter);
    }

    /**
     *
     */
    private static function loadDotEnv(): void
    {
        if (self::$env === null) {
            $names = empty($_SERVER['HTTP_TEST_ENVIRONMENT']) ? ['.env'] : ['.env.testing'];

            self::$env = Dotenv::createImmutable(self::getPath()->getRoot(), $names);
            try {
                self::$env->load();
            } catch (Exception) {
            }
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        if (array_key_exists(key: DefaultServiceInterface::class, array: self::$services)) {
            /** @var DefaultServiceInterface $defaultService */
            $defaultService = self::$services[self::$interfaceImplementations[DefaultServiceInterface::class]];

            foreach ($defaultService->getDelayedServices() ?? [] as $delayedService) {
                if (array_key_exists($delayedService, self::$services) && self::$services[$delayedService] instanceof InitialisableInterface) {
                    // TODO examples of delayed services. Can they be replaces by service priorities?
                    self::$services[$delayedService]->destroy();
                }
            }
        }

        if (array_key_exists(key: LoggerInterface::class, array: self::$services)) {
            // TODO can we replace this if service will be prirorities?
            self::$services[self::$interfaceImplementations[LoggerInterface::class]]->destroy();
        }

        /** @var ServiceInterface $service */
        foreach (array_reverse(array: self::$services ?? []) as $service) {
            if ($service instanceof InitialisableInterface) {
                $service->destroy();
            }
        }

        if (self::$hasBeenUpdated) {
            file_put_contents(self::$servicesCacheFile, serialize(self::$services));
            self::$hasBeenUpdated = false;
        }

        MinimalismObjectsFactory::terminate();

        session_write_close();
    }

    /**
     * @return EncrypterInterface|null
     */
    #[Pure]
    public function getEncrypter(): ?EncrypterInterface
    {
        // TODO do we really need separate methods getEncrypter, getLogger, getTransformer, getPath, can't we use create(string $interfaceName)?
        $encrypterImplementation = self::$interfaceImplementations[EncrypterInterface::class];
        return self::$services[$encrypterImplementation] ?? null;
    }

    /**
     * @return LoggerInterface
     */
    #[Pure]
    public function getLogger(): LoggerInterface
    {
        $loggerImplementation = self::$interfaceImplementations[LoggerInterface::class];
        return self::$services[$loggerImplementation];
    }

    /**
     * @return TransformerInterface|null
     */
    #[Pure]
    public function getTransformer(): ?TransformerInterface
    {
        $transformerImplementation = self::$interfaceImplementations[TransformerInterface::class];
        return self::$services[$transformerImplementation] ?? null;
    }

    /**
     * @return Path
     */
    public static function getPath(): Path
    {
        return self::$services[Path::class];
    }
}
