<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\TransformerInterface;
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

    /** @var string */
    private string $root;

    /** @var string|null */
    private ?string $url=null;

    /** @var Dotenv|null  */
    private ?Dotenv $env=null;

    /** @var string  */
    private string $servicesCacheFile;

    /** @var EncrypterInterface|null  */
    private ?EncrypterInterface $encrypter=null;

    /** @var TransformerInterface|null  */
    private ?TransformerInterface $transformer=null;

    /**
     * ServiceFactory constructor.
     */
    public function __construct()
    {
        $this->root = dirname(__DIR__, 5);
        $this->servicesCacheFile = $this->root
            . DIRECTORY_SEPARATOR . 'data'
            . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR . 'services.cache';

        if (PHP_SAPI !== 'cli') {
            $this->url = (((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || isset($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . (array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : '') . '/';
        }

        $this->loadServicesFromCache();
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
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return EncrypterInterface|null
     */
    public function getEncrypter(): ?EncrypterInterface
    {
        return $this->encrypter;
    }

    /**
     * @return TransformerInterface|null
     */
    public function getTransformer(): ?TransformerInterface
    {
        return $this->transformer;
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
                $this->transformer = $this->services[$serviceName];
            } elseif ($serviceReflection->implementsInterface(EncrypterInterface::class)){
                $this->encrypter = $this->services[$serviceName];
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

                        if (($param = $_ENV($serviceParameter->getName())) !== false) {
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
            $this->env = Dotenv::createImmutable($this->root);
            $this->env->load();
        }
    }
}