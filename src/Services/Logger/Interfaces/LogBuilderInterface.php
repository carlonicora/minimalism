<?php
namespace CarloNicora\Minimalism\Services\Logger\Interfaces;

use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;

interface LogBuilderInterface
{
    /**
     * LogBuilderInterface constructor.
     * @param ServicesFactory $service
     */
    public function __construct(ServicesFactory $service);

    /**
     * @param EventInterface $logMessage
     * @return EventInterface
     */
    public function log(EventInterface $logMessage) : EventInterface;

    /**
     * @param string $title
     */
    public function setEventsTitle(string $title): void;

    /**
     *
     */
    public function __destruct();
}