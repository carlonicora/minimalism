<?php
namespace CarloNicora\Minimalism\Services\Logger\Interfaces;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;

interface LogBuilderInterface
{
    /**
     * LogBuilderInterface constructor.
     * @param ServicesFactory $service
     */
    public function __construct(ServicesFactory $service);

    /**
     * @param LogMessageInterface $logMessage
     */
    public function log(LogMessageInterface $logMessage) : void;

    /**
     *
     */
    public function __destruct();
}