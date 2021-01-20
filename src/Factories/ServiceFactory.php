<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Interfaces\CacheInterface;
use CarloNicora\Minimalism\Interfaces\DataInterface;
use CarloNicora\Minimalism\Interfaces\DefaultServiceInterface;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
use CarloNicora\Minimalism\Services\Path;
use Dotenv\Dotenv;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use RuntimeException;

class ServiceFactory
{
    /** @var array|ServiceInterface[]  */
    private array $services = [];

    /** @var Dotenv|null  */
    private ?Dotenv $env=null;

    /** @var string  */
    private string $servicesCacheFile;

    /**
     * @throws Exception
     */
    public function initialise(): void
    {
        $this->servicesCacheFile = dirname(__DIR__, 5)
            . DIRECTORY_SEPARATOR . 'data'
            . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR . 'services.cache';

        $this->loadServicesFromCache();

        if (array_key_exists(Path::class, $this->services)) {
            $this->services[Path::class]->initialise();
        } else {
            $this->services[Path::class] = new Path();
        }

        if ($this->getPath()->getUrl() !== null) {
            $this->startSession();
        }

        $this->initialiseCoreServices();
        $this->initialiseBaseService();
    }

    /**
     *
     */
    public function __destruct()
    {
        /** @var ServiceInterface $service */
        foreach ($this->services ?? [] as $serviceName=>$service){
            if ($service !== null && !is_string($service)) {
                $service->destroy();
            }
        }

        file_put_contents($this->servicesCacheFile, serialize($this->services));
    }

    /**
     * @return Path
     */
    public function getPath(): Path
    {
        return $this->services[Path::class];
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

        $foundNamespace = null;
        $file = json_decode(file_get_contents($this->getPath()->getRoot() . DIRECTORY_SEPARATOR . 'composer.json'), true, 512, JSON_THROW_ON_ERROR);
        if (array_key_exists('autoload', $file) && array_key_exists('psr-4', $file['autoload'])) {
            foreach ($file['autoload']['psr-4'] as $namespace => $folder) {
                if ($folder === 'src/') {
                    $foundNamespace = $namespace;
                }
            }
        }

        if ($foundNamespace !== null){
            $servicePath = explode('\\', $foundNamespace);
            $serviceName = '\\' . $foundNamespace . $servicePath[count($servicePath)-2];

            $reflectedClass = new ReflectionClass($serviceName);
            if (!$reflectedClass->implementsInterface(DefaultServiceInterface::class)){
                throw new RuntimeException('The main service is not defined as Default Service', 500);
            }

            $this->services[$serviceName] = $this->create($serviceName);
            $this->services[DefaultServiceInterface::class] = $serviceName;
        }
    }

    /**
     * @throws Exception
     */
    private function initialiseCoreServices(): void
    {
        if (array_key_exists(EncrypterInterface::class, $this->services)
            &&
            array_key_exists(TransformerInterface::class, $this->services)
            &&
            array_key_exists(DataInterface::class, $this->services)
            &&
            array_key_exists(CacheInterface::class, $this->services)
        ) {
            return;
        }

        $servicesFiles = glob($this->getPath()->getRoot() . '/vendor/*/minimalism-service-*/src/*.php');

        $services = [];

        foreach ($servicesFiles ?? [] as $serviceFile){
            $potentialServiceName = strtolower(substr($serviceFile, strpos($serviceFile, 'minimalism-service-') + 19, strpos($serviceFile, '/src/')-strlen($serviceFile)));
            if ($potentialServiceName === strtolower(substr($serviceFile, strpos($serviceFile, '/src/') + 5, -4))){
                $src = file_get_contents($serviceFile);
                $namespace = null;
                if (preg_match('#^namespace\s+(.+?);$#sm', $src, $m)) {
                    $namespace = $m[1];
                }

                try {
                    $services[] = new ReflectionClass($namespace . '\\' . substr($serviceFile, strpos($serviceFile, '/src/') + 5, -4));
                } catch (ReflectionException) {}
            }
        }

        $this->searchInterface($services, CacheInterface::class);
        $this->searchInterface($services, EncrypterInterface::class);
        $this->searchInterface($services, TransformerInterface::class);
        $this->searchInterface($services, DataInterface::class);
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
        if (file_exists($this->servicesCacheFile)) {
            if (filemtime($this->servicesCacheFile) < (time() - 5 * 60)) {
                unlink($this->servicesCacheFile);
            } else {
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
    }

    /**
     *
     */
    private function startSession() : void
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (isset($_COOKIE['PHPSESSID'])) {
                $sessid = '';

                if (ini_get('session.use_cookies')) {
                    $sessid = $_COOKIE['PHPSESSID'];
                } elseif (!ini_get('session.use_only_cookies')) {
                    $sessid = $_GET['PHPSESSID'];
                }

                if (!preg_match('/^[a-z0-9]{32}$/', $sessid)) {
                    return;
                }
            }

            session_start();
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
                    /** @var ReflectionNamedType $parameter */
                    $parameter = $serviceParameter->getType();
                    try {
                        $reflect = new ReflectionClass($parameter->getName());
                        if ($reflect->implementsInterface(DefaultServiceInterface::class)){
                            $response[] = $this->services[$this->services[DefaultServiceInterface::class]];
                        } elseif ($reflect->implementsInterface(ServiceInterface::class) && $reflect->implementsInterface(EncrypterInterface::class)) {
                            $response[] = $this->services[$this->services[EncrypterInterface::class]];
                        } elseif ($reflect->implementsInterface(ServiceInterface::class) && $reflect->implementsInterface(DataInterface::class)) {
                            $response[] = $this->services[$this->services[DataInterface::class]];
                        } elseif ($reflect->implementsInterface(ServiceInterface::class) && $reflect->implementsInterface(TransformerInterface::class)) {
                            $response[] = $this->services[$this->services[TransformerInterface::class]];
                        } elseif ($reflect->implementsInterface(ServiceInterface::class) && $reflect->implementsInterface(CacheInterface::class)) {
                            $response[] = $this->services[$this->services[CacheInterface::class]];
                        } elseif ($reflect->implementsInterface(ServiceInterface::class)) {
                            $response[] = $this->create($parameter->getName());
                        }
                    } catch (ReflectionException) {
                        $this->loadDotEnv();
                        if (!$serviceParameter->isOptional()) {
                            $this->env->required($serviceParameter->getName())->notEmpty();
                            $response[] = $_ENV[$serviceParameter->getName()];
                        } elseif (array_key_exists($serviceParameter->getName(), $_ENV)){
                            $response[] = $_ENV[$serviceParameter->getName()];
                        } else {
                            $response[] = $serviceParameter->isDefaultValueAvailable() ? $serviceParameter->getDefaultValue() : null;
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
            $this->env = Dotenv::createImmutable($this->getPath()->getRoot());
            try {
                $this->env->load();
            } catch (Exception) {
            }
        }
    }
}