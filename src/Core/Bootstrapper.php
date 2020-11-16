<?php /** @noinspection UnserializeExploitsInspection */

namespace CarloNicora\Minimalism\Core;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Events\MinimalismInfoEvents;
use CarloNicora\Minimalism\Core\Modules\ErrorController;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Modules\Factories\ControllerFactory;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;
use function is_null;

/**
 * Class Bootstrapper
 * @package CarloNicora\Minimalism
 */
class Bootstrapper
{
    /** @var ServicesFactory  */
    private ServicesFactory $services;

    /** @var ModelInterface|string|null  */
    private $model;

    /** @var ControllerInterface|null  */
    private ?ControllerInterface $controller=null;

    /** @var ControllerFactory|null  */
    private ?ControllerFactory $controllerFactory=null;

    /** @var string|null  */
    private ?string $controllerClassName=null;

    /**
     * Bootstrapper constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->services = new ServicesFactory();
        $this->services->logger()->info()->log(MinimalismInfoEvents::START());

        $this->denyAccessToSpecificFileTypes();
    }

    /**
     * @param string $controllerClassName
     * @return Bootstrapper
     * @throws Exception
     */
    public function initialise(string $controllerClassName) : Bootstrapper
    {
        $this->controllerClassName = $controllerClassName;

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
                $this->controllerFactory = new ControllerFactory($this->services);
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
            } else {
                session_start();
                return;
            }


            session_start();
        }
    }

    /**
     * @return ServicesFactory
     * @throws Exception
     */
    private function loadServicesFromSession() : ServicesFactory
    {
        $events = $this->services->logger()->info()->getEvents();
        $this->services->logger()->info()->clearEvents();
        $this->services = $_SESSION['minimalismServices'];
        $this->services->logger()->info()->resetEvents($events);
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
     * @throws Exception
     */
    private function loadServicesFromCache() : ServicesFactory
    {
        $events = $this->services->logger()->info()->getEvents();
        $this->services->logger()->info()->clearEvents();
        $this->services = unserialize(file_get_contents($this->services->paths()->getCache()));
        $this->services->logger()->info()->resetEvents($events);
        $this->services->logger()->info()->log(MinimalismInfoEvents::SERVICES_LOADED_FROM_CACHE());

        return $this->services;
    }

    /**
     * @return ServicesFactory
     * @throws ConfigurationException|Exception
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
     * @param ModelInterface|string|null $model
     * @param array $parameterValueList
     * @param array $parameterValues
     * @return ControllerInterface
     */
    public function loadController($model=null, array $parameterValueList=[], array $parameterValues=[]): ControllerInterface
    {
        if ($this->controller === null) {
            if ($model !== null) {
                $this->setModel($model);
            }

            try {
                if (is_null($this->controllerFactory)) {
                    $this->services->logger()->error()->log(
                        MinimalismErrorEvents::BOOTSTRAPPER_NOT_INITIALISED()
                    )->throw();
                }

                $this->controller = $this->controllerFactory
                    ->loadController($this->controllerClassName)
                    ->initialiseParameters($parameterValueList, $parameterValues)
                    ->initialiseModel($this->model)
                    ->postInitialise();

            } catch (ConfigurationException|Exception $e) {
                $this->controller = new ErrorController($this->services);
                $this->controller
                    ->initialiseParameters()
                    ->initialiseModel('')
                    ->postInitialise();

                $code = $e->getCode() !== '' ? $e->getCode() : ResponseInterface::HTTP_STATUS_500;
                $this->controller->setException(new Exception($e->getMessage(), $code, $e));
            }
        }

        return $this->controller;
    }

    /**
     * @param ModelInterface|string $model
     * @param array $parameterValueList
     * @param array $parameterValues
     * @return ControllerInterface
     */
    public function reLoadController($model, array $parameterValueList=[], array $parameterValues=[]): ControllerInterface
    {
        $this->controller = null;
        return $this->loadController($model, $parameterValueList, $parameterValues);
    }

    /**
     * @param ModelInterface|string $model
     */
    public function setModel($model) : void
    {
        $this->model = $model;
    }

    /**
     * @param Exception $e
     * @noinspection ForgottenDebugOutputInspection
     */
    public function saveException(Exception $e): void
    {
        try {
            $this->services->logger()->error()->log(
                MinimalismErrorEvents::GENERIC_ERROR($e)
            );
        } catch (Exception $e) {
            error_log($e->getTraceAsString());
        }
    }
}
