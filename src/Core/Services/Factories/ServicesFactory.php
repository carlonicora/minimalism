<?php
namespace CarloNicora\Minimalism\Core\Services\Factories;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceFactoryInterface;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\SecurityInterface;
use CarloNicora\Minimalism\Services\Logger\Logger;
use CarloNicora\Minimalism\Services\ParameterValidator\ParameterValidator;
use CarloNicora\Minimalism\Services\Paths\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\Paths\Paths;
use Dotenv\Dotenv;
use Exception;
use JsonException;
use ReflectionClass;
use ReflectionException;

class ServicesFactory
{
    /** @var array */
    private array $services = [];

    /**
     * services constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->loadService(ServiceFactory::class);
        $this->paths()->initialiseDirectoryStructure();
        $this->loadService(\CarloNicora\Minimalism\Services\Logger\Factories\ServiceFactory::class);
        $this->loadService(\CarloNicora\Minimalism\Services\ParameterValidator\Factories\ServiceFactory::class);
    }

    /**
     * @return Paths
     */
    public function paths() : Paths
    {
        return $this->services[Paths::class];
    }

    /**
     * @return ParameterValidator
     * @throws Exception
     */
    public function parameterValidator() : ParameterValidator
    {
        return $this->service(ParameterValidator::class);
    }

    /**
     * @return Logger
     * @throws Exception
     */
    public function logger() : Logger
    {
        return $this->service(Logger::class);
    }

    /**
     * @throws ConfigurationException
     */
    public function initialise() : void
    {
        $env = Dotenv::createImmutable($this->paths()->getRoot());
        try{
            $env->load();
        } catch (Exception $e) {
        }

        foreach ($this->getServiceFactories() as $serviceFactoryClass){
            $this->loadService($serviceFactoryClass);
        }
    }

    /**
     * @param string $serviceName
     * @return mixed
     * @throws ServiceNotFoundException|Exception
     */
    public function service(string $serviceName)
    {
        if (!array_key_exists($serviceName, $this->services)){
            $this->logger()->error()->log(
                MinimalismErrorEvents::SERVICE_NOT_FOUND($serviceName)
            )->throw(ServiceNotFoundException::class);
        }

        return $this->services[$serviceName];
    }

    /**
     * @param string $serviceFactoryClass
     * @return ServiceInterface|null
     */
    public function loadService(string $serviceFactoryClass) : ?ServiceInterface
    {
        $serviceClass = '';
        $namespaceParts = explode('\\', $serviceFactoryClass);
        for ($counter=0; $counter<=count($namespaceParts)-3;$counter++){
            $serviceClass .=  $namespaceParts[$counter] . '\\';
        }
        $serviceClass .= $namespaceParts[count($namespaceParts)-3];

        if (!array_key_exists($serviceClass, $this->services)){
            /** @var ServiceFactoryInterface $service */
            $service = new $serviceFactoryClass($this);
            $this->services[$serviceClass] = $service->create($this);

            return $this->services[$serviceClass];
        }

        return null;
    }

    /**
     * @param string $serviceClass
     * @throws ConfigurationException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function loadDependency(string $serviceClass) : void
    {
        if (!array_key_exists($serviceClass, $this->services)){

            $serviceFactoryClass = '';
            $namespaceParts = explode('\\', $serviceClass);
            for ($counter=0; $counter<=count($namespaceParts)-2;$counter++){
                $serviceFactoryClass .=  $namespaceParts[$counter] . '\\';
            }
            $serviceFactoryClass .= 'Factories\\ServiceFactory';

            /** @var ServiceFactoryInterface $service */
            $service = new $serviceFactoryClass($this);
            $this->services[$serviceClass] = $service->create($this);
        }
    }

    /**
     * @return array
     */
    private function getServiceFactories() : array
    {
        $plugins =  glob(realpath('./vendor') . '/*/*/src/Factories/ServiceFactory.php');
        $builtIn = glob(realpath('./vendor') . '/*/*/src/Services/*/Factories/ServiceFactory.php');
        $internal = glob(realpath('./src') . '/Services/*/Factories/ServiceFactory.php');
        $microservice = glob(realpath('./src') . '/Factories/ServiceFactory.php');

        $files = array_unique(array_merge($plugins, $builtIn, $internal, $microservice));

        $response = [];

        foreach ($files as $fileName){
            /** @noinspection PhpIncludeInspection */
            require_once $fileName;
        }

        $classes = get_declared_classes();
        foreach($classes as $singleClass) {
            try {
                $reflect = new ReflectionClass($singleClass);
                if ($reflect->implementsInterface(ServiceFactoryInterface::class) && !$reflect->isAbstract()) {
                    $response[] = $singleClass;
                }
            } catch (ReflectionException $e) {
            }
        }

        return $response;
    }

    /**
     *
     */
    public function cleanNonPersistentVariables(): void
    {
        /** @var ServiceInterface $service */
        foreach ($this->services as $service) {
            $service->cleanNonPersistentVariables();
        }
    }

    /**
     * @param string $cookieName
     * @throws ConfigurationException
     */
    public function unserialiseCookies(string $cookieName) : void
    {
        try {
            $cookiesArray = json_decode($_COOKIE[$cookieName], true, 512, JSON_THROW_ON_ERROR);

            /** @var ServiceInterface $service */
            foreach ($this->services as $service) {
                $service->unserialiseCookies($cookiesArray);
            }
        } catch (JsonException $e) {
            setcookie($cookieName, 0);
        }
    }

    /**
     * @return string
     * @throws JsonException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function serialiseCookies() : string
    {
        $cookies = [];
        /** @var ServiceInterface $service */
        foreach ($this->services as $service) {
            $cookies[] =$service->serialiseCookies();
        }

        $allTheCookies = array_merge([], ...$cookies);

        return json_encode($allTheCookies, JSON_THROW_ON_ERROR, 512);
    }

    /**
     *
     */
    public function initialiseStatics() : void
    {
        /** @var ServiceInterface $service */
        foreach ($this->services as $service){
            $service->initialiseStatics($this);
        }
    }

    /**
     *
     */
    public function destroyStatics() : void
    {
        /** @var ServiceInterface $service */
        foreach ($this->services as $service){
            $service->destroyStatics();
        }
    }

    /**
     * @return SecurityInterface|null
     */
    public function getSecurityInterface() : ?SecurityInterface
    {
        foreach ($this->services as $service) {
            if (in_array(SecurityInterface::class, class_implements($service), true)){
                return $service;
            }
        }

        return null;
    }

    /**
     * @return EncrypterInterface|null
     */
    public function getEncrypterInterface() : ?EncrypterInterface
    {
        foreach ($this->services as $service) {
            if (in_array(EncrypterInterface::class, class_implements($service), true)){
                return $service;
            }
        }

        return null;
    }
}