<?php
namespace carlonicora\minimalism\services\factories;

use carlonicora\minimalism\exceptions\configurationException;
use carlonicora\minimalism\exceptions\serviceNotFoundException;
use carlonicora\minimalism\services\interfaces\serviceFactoryInterface;
use carlonicora\minimalism\services\interfaces\serviceInterface;
use carlonicora\minimalism\services\paths\factories\serviceFactory;
use carlonicora\minimalism\services\paths\paths;
use Dotenv\Dotenv;
use Exception;

class servicesFactory {
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
        $paths = $this->services[serviceFactory::class];

        $env = Dotenv::createImmutable($paths->getRoot());
        try{
            $env->load();
        } catch (Exception $e) {
            throw new configurationException('minimalism', $e->getMessage());
        }

        foreach ($this->getServices() as $serviceClass){
            if ($serviceClass !== self::class && $serviceClass !== serviceFactory::class) {
                $this->loadService($serviceClass);
            }
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
     * @param string $serviceClass
     * @throws configurationException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    private function loadService(string $serviceClass) : void {
        if (!array_key_exists($serviceClass, $this->services)){
            /** @var serviceFactoryInterface $service */
            $service = new $serviceClass();
            $this->services[$serviceClass] = $service->create($this);
        }
    }

    /**
     * @return array
     */
    private function getServices() : array {
        $external = glob(realpath('./vendor') . '/*/*/src/services/*/factories/serviceFactory.php');
        $internal = glob(realpath('./src') . '/services/*/factories/serviceFactory.php');

        $files = array_merge($internal, $external);

        $response = [];

        foreach ($files as $fileName){
            $response[] = $this->getClassNameFromFile($fileName);
        }

        return $response;
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function getClassNameFromFile(string $fileName) : string {
        $fp = fopen($fileName, 'rb');
        $class = $namespace = $buffer = '';
        $i = 0;
        while (!$class) {
            if (feof($fp)) {
                break;
            }

            $buffer .= fread($fp, 512);
            $tokens = token_get_all($buffer);

            if (strpos($buffer, '{') === false) {
                continue;
            }

            for ($iMax = count($tokens); $i< $iMax; $i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j=$i+1, $jMax = count($tokens); $j< $jMax; $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= '\\'.$tokens[$j][1];
                        } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }

                if ($tokens[$i][0] === T_CLASS) {
                    for ($j=$i+1, $jMax = count($tokens); $j< $jMax; $j++) {
                        if ($tokens[$j] === '{') {
                            $class = $tokens[$i+2][1];
                        }
                    }
                }
            }
        }

        return $namespace . '\\' . $class;
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
}