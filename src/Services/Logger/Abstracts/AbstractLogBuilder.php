<?php
namespace CarloNicora\Minimalism\Services\Logger\Abstracts;

use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Interfaces\LogBuilderInterface;

abstract class AbstractLogBuilder implements LogBuilderInterface
{
    /** @var ServicesFactory  */
    protected ServicesFactory $services;

    /** @var string  */
    protected string $logDirectory;

    /** @var string */
    protected string $title='';

    /**
     * AbstractLogBuilder constructor.
     * @param ServicesFactory $services
     */
    final public function __construct(ServicesFactory $services)
    {
        $this->services = $services;

        if ($services->paths() !== null) {
            $this->logDirectory = $services->paths()->getLog();
        }
    }

    /**
     *
     */
    public function __destruct()
    {
    }

    /**
     * @param EventInterface $logMessage
     * @return EventInterface
     */
    abstract public function log(EventInterface $logMessage): EventInterface;

    /**
     * @param string $title
     */
    public function setEventsTitle(string $title): void
    {
        $this->title = $title;
    }
}