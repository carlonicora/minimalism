<?php /** @noinspection UnserializeExploitsInspection */

namespace carlonicora\minimalism\core;

use carlonicora\minimalism\core\modules\interfaces\controllerInterface;
use carlonicora\minimalism\core\modules\factories\controllerFactory;
use carlonicora\minimalism\core\services\exceptions\configurationException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\logger\logger;
use carlonicora\minimalism\services\logger\objects\log;
use Exception;
use carlonicora\minimalism\core\modules\exceptions\prerequisiteException;
use JsonException;
use RuntimeException;
use Throwable;

/**
 * Class bootstrapper
 * @package carlonicora\minimalism
 */
class bootstrapper{
    use \carlonicora\minimalism\services\logger\traits\logger;

    /** @var servicesFactory  */
    private ?servicesFactory $services=null;

    /** @var string|null  */
    private ?string $modelName=null;

    /** @var string|null */
    public static ?string $servicesCache=null;

    /** @var controllerInterface|null  */
    private ?controllerInterface $controller=null;

    /**
     * bootstrapper constructor.
     */
    public function __construct() {
        $startLog = new log('Request started (' . ($_SERVER['REQUEST_URI'] ?? '') . ')');

        $this->denyAccessToSpecificFileTypes();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['minimalismServices'])){
            $this->services = $_SESSION['minimalismServices'];
            $servicesLog = new log('Services loaded from session');
        } else {
            self::$servicesCache = realpath('.') . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'services.cache';
            if (file_exists(self::$servicesCache) && filemtime(self::$servicesCache) > (time() - 5 * 60)) {
                /** @noinspection UnserializeExploitsInspection */
                $this->services = unserialize(file_get_contents(self::$servicesCache));
                self::$servicesCache = null;
                $servicesLog = new log('Services loaded from cache');
            } else {
                /** @noinspection NotOptimalIfConditionsInspection */
                if (file_exists(self::$servicesCache)){
                    unlink(self::$servicesCache);
                }

                try{
                    $this->services = new servicesFactory();
                    $this->services->initialise();

                    if (isset($_COOKIE['minimalismServices'])){
                        $this->services->unserialiseCookies($_COOKIE['minimalismServices']);
                    }
                    $servicesLog = new log('Services loaded from scratch');
                } catch (configurationException|JsonException $e) {
                    $this->writeError($e);
                    exit;
                }
            }
        }

        $this->services->cleanNonPersistentVariables();
        $this->services->initialiseStatics();
        $this->services->initialiseServicesLoader();

        /** @var logger $logger */
        /** @noinspection PhpUnhandledExceptionInspection */
        $logger = $this->services->service(logger::class);
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
            } catch (services\exceptions\serviceNotFoundException $e) {
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
        exit;
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
            /** @var logger $logger */
            $logger = $this->services->service(logger::class);
            $logger->flush();
        } catch (services\exceptions\serviceNotFoundException $e) {
        }
    }

    /**
     * @param string|null $modelName
     * @param array|null $parameterValueList
     * @param array|null $parameterValues
     * @return controllerInterface
     * @return Exception
     */
    public function loadController(string $modelName=null, array $parameterValueList=null, array $parameterValues=null): controllerInterface {
        if ($modelName !== null){
            $this->modelName = $modelName;
        }

        $controllerFactory = new controllerFactory();
        try {
            $controllerName = $controllerFactory->loadControllerName();
        } catch (prerequisiteException $e) {
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