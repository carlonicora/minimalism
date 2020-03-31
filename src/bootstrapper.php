<?php
namespace carlonicora\minimalism;

use carlonicora\minimalism\abstracts\abstractController;
use carlonicora\minimalism\controllers\apiController;
use carlonicora\minimalism\controllers\appController;
use carlonicora\minimalism\controllers\cliController;
use carlonicora\minimalism\exceptions\configurationException;
use carlonicora\minimalism\helpers\sessionManager;
use carlonicora\minimalism\abstracts\abstractConfigurations;
use carlonicora\minimalism\jsonapi\responses\errorResponse;
use Exception;
use RuntimeException;

/**
 * Class bootstrapper
 * @package carlonicora\minimalism
 */
class bootstrapper{
    /** @var abstractConfigurations $configurations */
    private abstractConfigurations $configurations;

    /** @var int */
    public const API_CONTROLLER=1;

    /** @var int */
    public const APP_CONTROLLER=2;

    /** @var int */
    public const CLI_CONTROLLER=3;

    /**
     * bootstrapper constructor.
     * @param string $configurationName
     * @param int $applicationType
     */
    public function __construct(string $configurationName, int $applicationType=self::API_CONTROLLER){
        $this->configurations = new $configurationName($applicationType);

        $sessionManager = new sessionManager();
        try {
            $sessionManager->loadFromSession($this->configurations);
        } catch (configurationException $e) {
            $errorResponse = new errorResponse(errorResponse::HTTP_STATUS_500, $e->getMessage());
            echo $errorResponse->toJson();
            exit;
        }
    }

    /**
     * @param null|string $modelName
     * @param null|array $parameterValueList
     * @param null|array $parameterValues
     * @return abstractController
     * @throws Exception
     */
    public function loadController(string $modelName=null, array $parameterValueList=null, array $parameterValues=null): abstractController {
        switch ($this->configurations->applicationType) {
            case self::API_CONTROLLER:
                $response = new apiController($this->configurations, $modelName, $parameterValueList, $parameterValues);
                break;
            case self::APP_CONTROLLER:
                $response = new appController($this->configurations, $modelName, $parameterValueList, $parameterValues);
                break;
            case self::CLI_CONTROLLER:
                $response = new cliController($this->configurations, $modelName, $parameterValueList, $parameterValues);
                break;
            default:
                throw new RuntimeException('A precise type of controller is required');
                break;
        }

        return $response;
    }

    /**
     * @return abstractConfigurations
     */
    public function getConfigurations(): abstractConfigurations{
        return $this->configurations;
    }
}