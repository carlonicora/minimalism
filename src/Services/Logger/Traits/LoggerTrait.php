<?php
namespace CarloNicora\Minimalism\Services\Logger\Traits;

use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Paths\Paths;
use Throwable;

trait LoggerTrait {
    /** @var array  */
    protected array $logFolders = [];

    /**
     * @param ServicesFactory $services
     * @throws ServiceNotFoundException
     */
    protected function loggerInitialise(ServicesFactory $services) : void {
        /** @var Paths $paths */
        $paths = $services->service(Paths::class);
        $this->logFolders = $paths->getLogFolders();
    }

    /**
     * @param int $code
     * @param string $message
     * @param Throwable|null $exception
     * @return string
     */
    private function loggerCreateMessage(int $code, string $message, ?Throwable $exception=null) : string {
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
     * @param Throwable|null $exception
     */
    protected function loggerWriteError(int $code, string $message, ?string $serviceName=null, ?Throwable $exception=null): void {
        foreach ($this->logFolders as $logFolder){
            $errorFile = $logFolder . ($serviceName ?? 'minimalism') . '.error.log';
            $errorMessage = $this->loggerCreateMessage($code, $message, $exception);

            $this->loggerWriteLog($errorMessage, $errorFile);
        }
    }

    /**
     * @param string $message
     */
    protected function loggerWriteTiming(string $message): void {
        foreach ($this->logFolders as $logFolder){
            $timingFile = $logFolder . 'timing.log';

            $this->loggerWriteLog($message, $timingFile);
        }
    }

    /**
     * @param string $message
     * @param string $fileName
     */
    private function loggerWriteLog(string $message, string $fileName) : void {
        /** @noinspection ForgottenDebugOutputInspection */
        error_log($message,3,$fileName);
    }
}