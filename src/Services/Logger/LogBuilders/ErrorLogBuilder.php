<?php
namespace CarloNicora\Minimalism\Services\Logger\LogBuilders;

use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;
use CarloNicora\Minimalism\Services\Logger\Abstracts\AbstractLogBuilder;

class ErrorLogBuilder extends AbstractLogBuilder
{
    /**
     * @param EventInterface $logMessage
     * @return EventInterface
     */
    public function log(EventInterface $logMessage): EventInterface
    {
        $errorMessage = $logMessage->generateMessage() . PHP_EOL;

        $errorFile = $this->logDirectory . $logMessage->getService() . '.error.log';

        /** @noinspection ForgottenDebugOutputInspection */
        error_log($errorMessage,3,$errorFile);

        return $logMessage;
    }
}