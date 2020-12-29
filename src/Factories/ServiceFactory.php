<?php
namespace CarloNicora\Minimalism\Factories;

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
     * ServiceFactory constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->servicesCacheFile = dirname(__DIR__, 5)
            . DIRECTORY_SEPARATOR . 'data'
            . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR . 'services.cache';

        $this->loadServicesFromCache();

        if (!array_key_exists(Path::class, $this->services)){
            $this->services[Path::class] = new Path();
        }

        if ($this->services['encrypter'] === null || $this->services['transformer'] === null){
            $this->searchCoreServices();
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        /** @var ServiceInterface $service */
        foreach ($this->services ?? [] as $service){
            $service->destroy();
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
        return $this->services['encrypter'] ?? null;
    }

    /**
     * @return TransformerInterface|null
     */
    public function getTransformer(): ?TransformerInterface
    {
        return $this->services['transformer'] ?? null;
    }

    /**
     * @throws Exception
     */
    private function searchCoreServices(): void
    {
        $servicesFiles = glob($this->getPath()->getRoot() . '/vendor/*/minimalism-service-*/src/*.php');

        foreach ($servicesFiles ?? []as $serviceFile){
            $potentialServiceName = strtolower(substr($serviceFile, strpos($serviceFile, 'minimalism-service-') + 19, strpos($serviceFile, '/src/')-strlen($serviceFile)));
            if ($potentialServiceName === strtolower(substr($serviceFile, strpos($serviceFile, '/src/') + 5, -4))){
                $src = file_get_contents($serviceFile);
                $namespace = null;
                if (preg_match('#^namespace\s+(.+?);$#sm', $src, $m)) {
                    $namespace = $m[1];
                }

                try {
                    $service = new ReflectionClass($namespace . '\\' . substr($serviceFile, strpos($serviceFile, '/src/') + 5, -4));
                    if ($this->getEncrypter() === null && $service->implementsInterface(EncrypterInterface::class)) {
                        $this->services['encrypter'] = $this->create($service->getName());
                    }
                    if ($this->getTransformer() === null && $service->implementsInterface(TransformerInterface::class)) {
                        $this->services['transformer'] = $this->create($service->getName());
                    }
                } catch (ReflectionException) {
                }
            }

            if ($this->services['encrypter'] === null || $this->services['transformer'] === null){
                return;
            }
        }
    }

    /**
     *
     */
    private function loadServicesFromCache(): void
    {
        if (file_exists($this->servicesCacheFile)){
            if (filemtime($this->servicesCacheFile) < (time() - 5 * 60)) {
                unlink($this->servicesCacheFile);
            } else {
                $serviceFile = file_get_contents($this->servicesCacheFile);

                if ($serviceFile !== false){
                    $this->services = unserialize($serviceFile, [true]);
                }
            }
        }
    }

    /**
     * @param string $serviceName
     * @return ServiceInterface
     * @throws Exception
     */
    public function create(string $serviceName): ServiceInterface
    {
        if (!array_key_exists($serviceName, $this->services)) {
            $parameters = $this->loadDependencies($serviceName);

            $this->services[$serviceName] = new $serviceName(...$parameters);

            $serviceReflection = new ReflectionClass($this->services[$serviceName]);
            if ($serviceReflection->implementsInterface(TransformerInterface::class)){
                $this->services['transformer'] = $this->services[$serviceName];
            } elseif ($serviceReflection->implementsInterface(EncrypterInterface::class)){
                $this->services['encrypter'] = $this->services[$serviceName];
            }
            $serviceReflection = null;
        }

        return $this->services[$serviceName];
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
                        if ($reflect->implementsInterface(ServiceInterface::class)) {
                            $response[] = $this->create($parameter->getName());
                        }
                    } catch (ReflectionException) {
                        $this->loadDotEnv();
                        if (!$serviceParameter->isOptional()) {
                            $this->env->required($serviceParameter->getName())->notEmpty();
                        }

                        if (($param = $_ENV[$serviceParameter->getName()]) !== false) {
                            $response[] = $param;
                        } else {
                            $response[] = null;
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
            $this->env->load();
        }
    }
}