<?php
namespace CarloNicora\Minimalism\Services\Logger\LogBuilders;

use CarloNicora\Minimalism\Services\Logger\Abstracts\AbstractLogBuilder;
use CarloNicora\Minimalism\Services\Logger\Interfaces\LogMessageInterface;

class ErrorLogBuilder extends AbstractLogBuilder
{
    public function log(LogMessageInterface $logMessage): void
    {
        $errorMessage = $logMessage->generateMessage();

        $errorFile = $this->logDirectory . $logMessage->getService() . '.error.log';

        /** @noinspection ForgottenDebugOutputInspection */
        error_log($errorMessage,3,$errorFile);
    }
}