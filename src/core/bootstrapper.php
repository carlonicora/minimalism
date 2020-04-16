<?php /** @noinspection UnserializeExploitsInspection */

namespace carlonicora\minimalism\core;

use carlonicora\minimalism\core\modules\interfaces\controllerInterface;
use carlonicora\minimalism\core\modules\factories\controllerFactory;
use carlonicora\minimalism\core\services\exceptions\configurationException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use Exception;
use carlonicora\minimalism\core\modules\exceptions\prerequisiteException;
use JsonException;
use RuntimeException;

/**
 * Class bootstrapper
 * @package carlonicora\minimalism
 */
class bootstrapper{
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
        $this->denyAccessToSpecificFileTypes();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['minimalismServices'])){
            $this->services = $_SESSION['minimalismServices'];
        } else {
            self::$servicesCache = realpath('.') . DIRECTORY_SEPARATOR . 'services.cache';
            if (file_exists(self::$servicesCache)){
                if (filemtime(self::$servicesCache) > (time() - 5 * 60)) {
                    /** @noinspection UnserializeExploitsInspection */
                    $this->services = unserialize(file_get_contents(self::$servicesCache));
                    self::$servicesCache = null;
                } else {
                    unlink(self::$servicesCache);
                }
            }
        }

        try {
            if ($this->services !== null){
                $this->services->cleanNonPersistentVariables();
            } else {
                $this->services = new servicesFactory();
                $this->services->initialise();

                if (isset($_COOKIE['minimalismServices'])){
                    $this->services->unserialiseCookies($_COOKIE['minimalismServices']);
                }
            }
            $this->services->initialiseStatics();
            $this->services->initialiseServicesLoader();
        } catch (configurationException|JsonException $e) {
            $this->writeError($e);
            exit;
        }
    }

    /**
     * @param Exception $e
     */
    private function writeError(Exception $e) : void {
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
        if ($_SERVER['REQUEST_URI'] !== null) {
            $fileType = substr(strrchr($_SERVER['REQUEST_URI'], '.'), 1);

            if (true === in_array(strtolower($fileType), ['jpg', 'png', 'css', 'js'], true)) {
                $this->writeError(new Exception('Filetype not supported', 404));
                exit;
            }
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