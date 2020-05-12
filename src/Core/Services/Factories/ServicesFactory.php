<?php
namespace CarloNicora\Minimalism\Core\Services\Factories;

use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServicesLoader;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceFactoryInterface;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Services\Paths\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\Paths\Paths;
use Dotenv\Dotenv;
use Exception;
use JsonException;
use ReflectionClass;
use ReflectionException;

class ServicesFactory {
    /** @var array */
    private array $services = [];

    /**
     * services constructor.
     */
    public function __construct() {
    }

    /**
     * @throws ConfigurationException
     */
    public function initialise() : void {
        $this->loadService(ServiceFactory::class);

        /** @var Paths $paths */
        $paths = $this->services[Paths::class];

        $env = Dotenv::createImmutable($paths->getRoot());
        try{
            $env->load();
        } catch (Exception $e) {
            throw new ConfigurationException('minimalism', $e->getMessage(), ConfigurationException::ERROR_CONFIGURATION_FILE_ERROR);
        }

        foreach ($this->getServiceFactories() as $serviceFactoryClass){
            $this->loadService($serviceFactoryClass);
        }
    }

    /**
     *
     */
    public function initialiseServicesLoader() : void {
        /** @var Paths $paths */
        try {
            $paths = $this->service(Paths::class);

            /** @var AbstractServicesLoader $serviceLoader */
            $serviceLoader = $paths->getNamespace() . 'servicesLoader';

            if (class_exists($serviceLoader)){
                $serviceLoader::initialise($this);
            }
        } catch (ServiceNotFoundException|JsonException $e) {
        }
    }

    /**
     * @param string $serviceName
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function service(string $serviceName) {
        if (!array_key_exists($serviceName, $this->services)){
            throw new ServiceNotFoundException($serviceName);
        }

        return $this->services[$serviceName];
    }

    /**
     * @param string $serviceFactoryClass
     */
    public function loadService(string $serviceFactoryClass) : void {
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
        }
    }

    /**
     * @param string $serviceClass
     * @throws ConfigurationException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function loadDependency(string $serviceClass) : void {
        if (!array_key_exists($serviceClass, $this->services)){

            $serviceFactoryClass = '';
            $namespaceParts = explode('\\', $serviceClass);
            for ($counter=0; $counter<=count($namespaceParts)-2;$counter++){
                $serviceFactoryClass .=  $namespaceParts[$counter] . '\\';
            }
            $serviceFactoryClass .= 'factories\\serviceFactory';

            /** @var ServiceFactoryInterface $service */
            $service = new $serviceFactoryClass($this);
            $this->services[$serviceClass] = $service->create($this);
        }
    }

    /**
     * @return array
     */
    private function getServiceFactories() : array {
        $minimalism = glob(realpath('./vendor') . '/carlonicora/minimalism/src/services/*/factories/serviceFactory.php');
        $plugins =  glob(realpath('./vendor') . '/*/*/src/factories/serviceFactory.php');
        $builtIn = glob(realpath('./vendor') . '/*/*/src/services/*/factories/serviceFactory.php');
        $internal = glob(realpath('./src') . '/services/*/factories/serviceFactory.php');

        $files = array_unique(array_merge($minimalism, $plugins, $builtIn, $internal));

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
    public function cleanNonPersistentVariables(): void{
        /** @var ServiceInterface $service */
        foreach ($this->services as $service) {
            $service->cleanNonPersistentVariables();
        }
    }

    /**
     * @param string $cookies
     * @throws JsonException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function unserialiseCookies(string $cookies) : void {
        $cookiesArray = json_decode($cookies, true, 512, JSON_THROW_ON_ERROR);
        /** @var ServiceInterface $service */
        foreach ($this->services as $service) {
            $service->unserialiseCookies($cookiesArray);
        }
    }

    /**
     * @return string
     * @throws JsonException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function serialiseCookies() : string {
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
    public function initialiseStatics() : void {
        /** @var ServiceInterface $service */
        foreach ($this->services as $service){
            $service->initialiseStatics($this);
        }
    }

    /**
     *
     */
    public function destroyStatics() : void {
        /** @var ServiceInterface $service */
        foreach ($this->services as $service){
            $service->destroyStatics();
        }
    }
}