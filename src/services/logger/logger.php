<?php
namespace carlonicora\minimalism\services\logger;

use carlonicora\minimalism\core\services\abstracts\abstractService;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;
use carlonicora\minimalism\services\logger\configurations\loggerConfigurations;

class logger extends abstractService {
    /** @var loggerConfigurations  */
    private loggerConfigurations $configData;


    /**
     * abstractApiCaller constructor.
     * @param serviceConfigurationsInterface $configData
     * @param servicesFactory $services
     */
    public function __construct(serviceConfigurationsInterface $configData, servicesFactory $services){
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->configData = $configData;
    }

    /**
     * @param string $message
     * @param bool $trace
     * @return string
     */
    private function buildMessage(string $message, bool $trace): string {
        $response = date('Y-m-d H:i:s') . ' - ' . $message;
        if ($trace) {
            $backtrace = debug_backtrace();
            $response .= ' (' . $backtrace[1]['file'] . ' > ' . $backtrace[1]['line'] . ')';
        }
        $response .=  PHP_EOL;

        return $response;
    }

    /**
     * @param string $errorMessage
     * @param bool $trace
     */
    public function logError(string $errorMessage, bool $trace=true): void{
        $message = $this->buildMessage($errorMessage, $trace);

        /** @noinspection ForgottenDebugOutputInspection */
        error_log($message, 3, $this->configData->errorLog);
    }

    /**
     * @param string $notificationMessage
     * @param bool $trace
     */
    public function logNotification(string $notificationMessage, bool $trace=true): void{
        $message = $this->buildMessage($notificationMessage, $trace);

        /** @noinspection ForgottenDebugOutputInspection */
        error_log($message, 3, $this->configData->notificationLog);
    }
}