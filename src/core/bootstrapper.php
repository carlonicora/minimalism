<?php /** @noinspection UnserializeExploitsInspection */

namespace carlonicora\minimalism\core;

use carlonicora\minimalism\core\controllers\apiController;
use carlonicora\minimalism\core\controllers\appController;
use carlonicora\minimalism\core\controllers\cliController;
use carlonicora\minimalism\core\controllers\interfaces\controllerInterface;
use carlonicora\minimalism\core\exceptions\configurationException;
use carlonicora\minimalism\core\jsonapi\responses\dataResponse;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\jsonapi\responses\errorResponse;
use Exception;

/**
 * Class bootstrapper
 * @package carlonicora\minimalism
 */
class bootstrapper{
    public const API_CONTROLLER=1;
    public const APP_CONTROLLER=2;
    public const CLI_CONTROLLER=3;

    /** @var servicesFactory  */
    private ?servicesFactory $services=null;

    /** @var string|null  */
    private ?string $modelName=null;

    /** @var int  */
    private int $controllerType;

    /** @var string|null */
    public static ?string $servicesCache=null;

    /**
     * bootstrapper constructor.
     * @param int $controllerType
     */
    public function __construct(int $controllerType=self::API_CONTROLLER) {
        $this->denyAccessToSpecificFileTypes();

        $this->controllerType = $controllerType;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['minimalismServices'])){
            $this->services = $_SESSION['minimalismServices'];
        } else {
            self::$servicesCache = realpath('.') . DIRECTORY_SEPARATOR . 'services.cache';
            if (file_exists(self::$servicesCache) && filemtime(self::$servicesCache) > (time() - 5 * 60)) {
                /** @noinspection UnserializeExploitsInspection */
                $this->services = unserialize(file_get_contents(self::$servicesCache));
                self::$servicesCache = null;
            }
        }

        try {
            if ($this->services !== null){
                $this->services->cleanNonPersistentVariables();
                $this->services->initialiseStatics();
            } else {
                $this->services = new servicesFactory();
                $this->services->initialise();

                if (isset($_COOKIE['minimalismServices'])){
                    $this->services->unserialiseCookies($_COOKIE['minimalismServices']);
                }
            }
        } catch (configurationException $e) {
            $this->returnError(new errorResponse(errorResponse::HTTP_STATUS_500, $e->getMessage()));
            exit;
        }
    }

    private function denyAccessToSpecificFileTypes() : void {
        $fileType = substr(strrchr($_SERVER['REQUEST_URI'], '.'), 1);

        if (true === in_array(strtolower($fileType), ['jpg', 'png', 'css', 'js'], true)){
            $this->returnError(new errorResponse(errorResponse::HTTP_STATUS_404));
            exit;
        }
    }

    /**
     * @param array|null $parameterValueList
     * @param array|null $parameterValues
     * @return controllerInterface
     */
    public function loadController(array $parameterValueList=null, array $parameterValues=null): controllerInterface {
        try {
            switch ($this->controllerType) {
                case self::APP_CONTROLLER:
                    $response = new appController($this->services, $this->modelName, $parameterValueList, $parameterValues);
                    break;
                case self::CLI_CONTROLLER:
                    $response = new cliController($this->services, $this->modelName, $parameterValueList, $parameterValues);
                    break;
                default:
                    $response = new apiController($this->services, $this->modelName, $parameterValueList, $parameterValues);
                    break;
            }
        } catch (Exception $e) {
            $this->returnError(new errorResponse($e->getCode(), $e->getMessage()));
            exit;
        }

        return $response;
    }

    /**
     * @param errorResponse $error
     */
    private function returnError(errorResponse $error): void {
        $code = $error->getStatus();
        $GLOBALS['http_response_code'] = $code;

        header(dataResponse::generateProtocol() . ' ' . $code . ' ' . $error->generateText());
        exit;
    }

    /**
     * @param string $modelName
     */
    public function setModel(string $modelName) : void {
        $this->modelName = $modelName;
    }
}