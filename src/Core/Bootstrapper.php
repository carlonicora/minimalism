<?php /** @noinspection UnserializeExploitsInspection */

namespace CarloNicora\Minimalism\Core;

use CarloNicora\Minimalism\Core\Modules\ErrorController;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Modules\Factories\ControllerFactory;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Events\MinimalismInfoEvents;
use Exception;

/**
 * Class Bootstrapper
 * @package CarloNicora\Minimalism
 */
class Bootstrapper
{
    /** @var ServicesFactory  */
    private ServicesFactory $services;

    /** @var string|null  */
    private ?string $modelName=null;

    /** @var ControllerInterface|null  */
    private ?ControllerInterface $controller=null;

    /** @var ControllerFactory  */
    private ControllerFactory $controllerFactory;

    /**
     * Bootstrapper constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->services = new ServicesFactory();
        $this->services->logger()->info()->log(MinimalismInfoEvents::START());

        $this->controllerFactory = new ControllerFactory($this->services);

        $this->denyAccessToSpecificFileTypes();
    }

    /**
     * @return Bootstrapper
     */
    public function initialise() : Bootstrapper
    {
        if ($this->controller === null) {
            $this->startSession();

            if (isset($_SESSION['minimalismServices'])) {
                $this->services = $this->loadServicesFromSession();
            } elseif ($this->areServicesCached()) {
                $this->services = $this->loadServicesFromCache();
            } else {
                $this->services = $this->createServices();
            }

            if ($this->controller === null) {
                $this->services->cleanNonPersistentVariables();
                $this->services->initialiseStatics();
            }
        }

        return $this;
    }

    /**
     *
     */
    private function denyAccessToSpecificFileTypes() : void
    {
        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $fileType = substr(strrchr($_SERVER['REQUEST_URI'], '.'), 1);

            if (true === in_array(strtolower($fileType), ['jpg', 'png', 'css', 'js', 'ico'], true)) {
                $this->controller = new ErrorController($this->services);
                $this->controller->setException(new Exception('Filetype not supported', 404));
            }
        }
    }

    /**
     *
     */
    private function startSession() : void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @return ServicesFactory
     */
    private function loadServicesFromSession() : ServicesFactory
    {
        $events = $this->services->logger()->info()->getEvents();
        $this->services = $_SESSION['minimalismServices'];
        $this->services->logger()->info()->setEvents($events);
        $this->services->logger()->info()->log(MinimalismInfoEvents::SERVICES_LOADED_FROM_SESSION());

        return $this->services;
    }

    /**
     * @return bool
     */
    private function areServicesCached() : bool
    {
        if (file_exists($this->services->paths()->getCache())) {
            if (filemtime($this->services->paths()->getCache()) < (time() - 5 * 60)){
                unlink($this->services->paths()->getCache());
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * @return ServicesFactory
     */
    private function loadServicesFromCache() : ServicesFactory
    {
        $events = $this->services->logger()->info()->getEvents();
        $this->services = unserialize(file_get_contents($this->services->paths()->getCache()));
        $this->services->logger()->info()->setEvents($events);
        $this->services->logger()->info()->log(MinimalismInfoEvents::SERVICES_LOADED_FROM_CACHE());

        return $this->services;
    }

    /**
     * @return ServicesFactory
     * @throws ConfigurationException
     */
    private function createServices() : ServicesFactory
    {
        try{
            $this->services->initialise();

            if (isset($_COOKIE['minimalismServices'])){
                $this->services->unserialiseCookies('minimalismServices');
            }
            $this->services->logger()->info()->log(MinimalismInfoEvents::SERVICES_INITIALISED());
        } catch (ConfigurationException $e) {
            $this->controller = new ErrorController($this->services);
            $this->controller->setException(new Exception($e->getMessage(), 500, $e));
        }

        return $this->services;
    }

    /**
     * @param string|null $modelName
     * @param array $parameterValueList
     * @param array $parameterValues
     * @return ControllerInterface
     * @return Exception
     */
    public function loadController(string $modelName=null, array $parameterValueList=[], array $parameterValues=[]): ControllerInterface
    {
        if ($this->controller === null) {
            if ($modelName !== null) {
                $this->setModel($modelName);
            }

            try {
                $this->controller = $this->controllerFactory
                    ->loadController()
                    ->initialiseParameters($parameterValueList, $parameterValues)
                    ->initialiseModel($this->modelName);

            } catch (ConfigurationException $e) {
                $this->controller = new ErrorController($this->services);
                $this->controller->setException(new Exception($e->getMessage(), 500, $e));
            }
        }

        return $this->controller;
    }

    /**
     * @param string $modelName
     */
    public function setModel(string $modelName) : void
    {
        $this->modelName = $modelName;
    }
}