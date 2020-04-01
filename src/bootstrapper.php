<?php
namespace carlonicora\minimalism;

use carlonicora\minimalism\controllers\apiController;
use carlonicora\minimalism\controllers\appController;
use carlonicora\minimalism\controllers\cliController;
use carlonicora\minimalism\controllers\interfaces\controllerInterface;
use carlonicora\minimalism\exceptions\configurationException;
use carlonicora\minimalism\services\factories\servicesFactory;
use carlonicora\minimalism\jsonapi\responses\errorResponse;
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
    private servicesFactory $services;

    /** @var string|null  */
    private ?string $modelName=null;

    /** @var int  */
    private int $controllerType;

    /**
     * bootstrapper constructor.
     * @param int $controllerType
     */
    public function __construct(int $controllerType=self::API_CONTROLLER) {
        $this->controllerType = $controllerType;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->services = new servicesFactory();

        try {
            if (isset($_SESSION['minimalismServices'])){
                $this->services = $_SESSION['minimalismServices'];
                $this->services->cleanNonPersistentVariables();
                $this->services->initialiseStatics();
            } else {
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
        echo $error->toJson();
        exit;
    }

    /**
     * @param string $modelName
     */
    public function setModel(string $modelName) : void {
        $this->modelName = $modelName;
    }
}