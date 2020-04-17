<?php
namespace carlonicora\minimalism\services\logger\traits;

use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\paths\paths;
use Exception;

trait logger {
    /** @var array  */
    protected array $logFolders = [];

    /**
     * @param servicesFactory $services
     * @throws serviceNotFoundException
     */
    protected function loggerInitialise(servicesFactory $services) : void {
        /** @var paths $paths */
        $paths = $services->service(paths::class);
        $this->logFolders = $paths->getLogFolders();
    }

    /**
     * @param int $code
     * @param string $message
     * @param Exception|null $exception
     * @return string
     */
    private function loggerCreateMessage(int $code, string $message, ?Exception $exception=null) : string {
        $response = date('Y-m-d H:i:s') . ' - ' . $code  . ' - '. $message . PHP_EOL;
        if ($exception !== null) {
            $response .= $exception->getTraceAsString() . PHP_EOL;
        }
        $response .=  PHP_EOL;

        return $response;
    }

    /**
     * @param int $code
     * @param string $message
     * @param string|null $serviceName
     * @param Exception|null $exception
     */
    protected function loggerWriteError(int $code, string $message, ?string $serviceName=null, ?Exception $exception=null): void {
        foreach ($this->logFolders as $logFolder){
            $errorFile = $logFolder . ($serviceName ?? 'minimalism') . 'log';

            /** @noinspection ForgottenDebugOutputInspection */
            error_log(
                $this->loggerCreateMessage($code, $message, $exception),
                3,
                $errorFile
            );
        }
    }
}