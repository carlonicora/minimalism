<?php /** @noinspection UnserializeExploitsInspection */

namespace CarloNicora\Minimalism\Core;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Modules\Factories\ControllerFactory;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Core\Modules\Exceptions\PrerequisiteException;
use CarloNicora\Minimalism\Services\Logger\Logger;
use CarloNicora\Minimalism\Services\Logger\Objects\Log;
use CarloNicora\Minimalism\Services\Logger\Traits\LoggerTrait;
use Exception;
use JsonException;
use RuntimeException;
use Throwable;

/**
 * Class Bootstrapper
 * @package CarloNicora\Minimalism
 */
class Bootstrapper{
    use LoggerTrait;

    /** @var ServicesFactory  */
    private ?ServicesFactory $services=null;

    /** @var string|null  */
    private ?string $modelName=null;

    /** @var string|null */
    public static ?string $servicesCache=null;

    /** @var ControllerInterface|null  */
    private ?ControllerInterface $controller=null;

    /**
     * Bootstrapper constructor.
     * @throws Exception
     */
    public function __construct() {
        $startLog = new Log('Request started (' . ($_SERVER['REQUEST_URI'] ?? '') . ')');

        $this->denyAccessToSpecificFileTypes();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['minimalismServices'])){
            $this->services = $_SESSION['minimalismServices'];
            $servicesLog = new Log('Services loaded from session');
        } else {
            self::$servicesCache = realpath('.') . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'services.cache';
            if (file_exists(self::$servicesCache) && filemtime(self::$servicesCache) > (time() - 5 * 60)) {
                /** @noinspection UnserializeExploitsInspection */
                $this->services = unserialize(file_get_contents(self::$servicesCache));
                self::$servicesCache = null;
                $servicesLog = new Log('Services loaded from cache');
            } else {
                /** @noinspection NotOptimalIfConditionsInspection */
                if (file_exists(self::$servicesCache)){
                    unlink(self::$servicesCache);
                }

                try{
                    $this->services = new ServicesFactory();
                    $this->services->initialise();

                    if (isset($_COOKIE['minimalismServices'])){
                        $this->services->unserialiseCookies($_COOKIE['minimalismServices']);
                    }
                    $servicesLog = new Log('Services loaded from scratch');
                } catch (ConfigurationException|JsonException $e) {
                    $this->writeError($e);
                    throw $e;
                }
            }
        }

        $this->services->cleanNonPersistentVariables();
        $this->services->initialiseStatics();
        $this->services->initialiseServicesLoader();

        /** @var Logger $logger */
        /** @noinspection PhpUnhandledExceptionInspection */
        $logger = $this->services->service(Logger::class);
        $logger->addSystemEvent($startLog);
        $logger->addSystemEvent($servicesLog);
    }

    /**
     * @param Throwable $e
     */
    public function writeError(Throwable $e) : void {
        if ($this->services !== null) {
            try {
                $this->loggerInitialise($this->services);
                $this->loggerWriteError($e->getCode(), $e->getMessage(), 'minimalism', $e);
            } catch (services\Exceptions\ServiceNotFoundException $e) {
            }
        }

        if ($this->controller !== null) {
            $this->controller->writeException($e);
        } else {
            $errorCode = $e->getCode() ?? 500;
            $GLOBALS['http_response_code'] = $errorCode;
            $header = ($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1') . ' ' . $errorCode . ' ' . $e->getMessage();
            header($header);
            echo $e->getMessage();
        }
    }

    /**
     *
     */
    private function denyAccessToSpecificFileTypes() : void {
        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $fileType = substr(strrchr($_SERVER['REQUEST_URI'], '.'), 1);

            if (true === in_array(strtolower($fileType), ['jpg', 'png', 'css', 'js', 'ico'], true)) {
                $this->writeError(new Exception('Filetype not supported', 404));
                exit;
            }
        }
    }

    /**
     *
     */
    public function __destruct(){
        try {
            /** @var Logger $logger */
            $logger = $this->services->service(Logger::class);
            $logger->flush();
        } catch (services\Exceptions\ServiceNotFoundException $e) {
        }
    }

    /**
     * @param string|null $modelName
     * @param array|null $parameterValueList
     * @param array|null $parameterValues
     * @return ControllerInterface
     * @return Exception
     */
    public function loadController(string $modelName=null, array $parameterValueList=null, array $parameterValues=null): ControllerInterface {
        if ($modelName !== null){
            $this->modelName = $modelName;
        }

        $controllerFactory = new ControllerFactory();
        try {
            $controllerName = $controllerFactory->loadControllerName();
        } catch (PrerequisiteException $e) {
            throw new RuntimeException($e->getMessage());
        }

        return new $controllerName($this->services, $this->modelName, $parameterValueList, $parameterValues);
    }

    /**
     * @param string $modelName
     */
    public function setModel(string $modelName) : void {
        $this->modelName = $modelName;
    }
}