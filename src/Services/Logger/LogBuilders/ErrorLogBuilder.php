<?php
namespace CarloNicora\Minimalism\Services\Logger\LogBuilders;

use CarloNicora\Minimalism\Services\Logger\Abstracts\AbstractLogBuilder;
use CarloNicora\Minimalism\Services\Logger\Interfaces\LogMessageInterface;

class ErrorLogBuilder extends AbstractLogBuilder
{
    /**
     * @param LogMessageInterface $logMessage
     * @return LogMessageInterface
     */
    public function log(LogMessageInterface $logMessage): LogMessageInterface
    {
        $errorMessage = $logMessage->generateMessage();

        $errorFile = $this->logDirectory . $logMessage->getService() . '.error.log';

        /** @noinspection ForgottenDebugOutputInspection */
        error_log($errorMessage,3,$errorFile);

        return $logMessage;
    }
}