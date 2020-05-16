<?php
namespace CarloNicora\Minimalism\Services\Logger\Abstracts;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Interfaces\LogBuilderInterface;
use CarloNicora\Minimalism\Services\Logger\Interfaces\LogMessageInterface;

abstract class AbstractLogBuilder implements LogBuilderInterface
{
    /** @var ServicesFactory  */
    protected ServicesFactory $services;

    /** @var string  */
    protected string $logDirectory;

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
     * @param LogMessageInterface $logMessage
     */
    abstract public function log(LogMessageInterface $logMessage): void;
}