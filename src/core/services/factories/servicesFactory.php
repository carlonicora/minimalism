<?php
namespace carlonicora\minimalism\core\services\factories;

use carlonicora\minimalism\core\services\exceptions\configurationException;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\abstracts\abstractServicesLoader;
use carlonicora\minimalism\core\services\interfaces\serviceFactoryInterface;
use carlonicora\minimalism\core\services\interfaces\serviceInterface;
use carlonicora\minimalism\core\traits\filesystem;
use carlonicora\minimalism\services\paths\factories\serviceFactory;
use carlonicora\minimalism\services\paths\paths;
use Dotenv\Dotenv;
use Exception;

class servicesFactory {
    use filesystem;

    /** @var array */
    private array $services = [];

    /**
     * services constructor.
     */
    public function __construct() {
    }

    /**
     * @throws configurationException
     */
    public function initialise() : void {
        $this->loadService(serviceFactory::class);

        /** @var paths $paths */
        $paths = $this->services[paths::class];

        $env = Dotenv::createImmutable($paths->getRoot());
        try{
            $env->load();
        } catch (Exception $e) {
            throw new configurationException('minimalism', $e->getMessage());
        }

        foreach ($this->getServiceFactories() as $serviceFactoryClass){
            $this->loadService($serviceFactoryClass);
        }
    }

    /**
     *
     */
    public function initialiseServicesLoader() : void {
        /** @var paths $paths */
        try {
            $paths = $this->service(paths::class);

            /** @var abstractServicesLoader $serviceLoader */
            $serviceLoader = $paths->getNamespace() . 'servicesLoader';

            if (class_exists($serviceLoader)){
                $serviceLoader::initialise($this);
            }
        } catch (serviceNotFoundException $e) {
        }
    }

    /**
     * @param string $serviceName
     * @return mixed
     * @throws serviceNotFoundException
     */
    public function service(string $serviceName) {
        if (!array_key_exists($serviceName, $this->services)){
            throw new serviceNotFoundException($serviceName);
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
            /** @var serviceFactoryInterface $service */
            $service = new $serviceFactoryClass($this);
            $this->services[$serviceClass] = $service->create($this);
        }
    }

    /**
     * @param string $serviceClass
     * @throws configurationException
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

            /** @var serviceFactoryInterface $service */
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
            $response[] = $this->getClassNameFromFile($fileName);
        }

        return $response;
    }

    /**
     *
     */
    public function cleanNonPersistentVariables(): void{
        /** @var serviceInterface $service */
        foreach ($this->services as $service) {
            $service->cleanNonPersistentVariables();
        }
    }

    /**
     * @param string $cookies
     */
    public function unserialiseCookies(string $cookies) : void {
        $cookiesArray = json_decode($cookies, true, 512, JSON_THROW_ON_ERROR);
        /** @var serviceInterface $service */
        foreach ($this->services as $service) {
            $service->unserialiseCookies($cookiesArray);
        }
    }

    /**
     * @return string
     */
    public function serialiseCookies() : string {
        $cookies = [];
        /** @var serviceInterface $service */
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
        /** @var serviceInterface $service */
        foreach ($this->services as $service){
            $service->initialiseStatics($this);
        }
    }
}