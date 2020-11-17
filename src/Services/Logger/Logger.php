<?php
namespace CarloNicora\Minimalism\Services\Logger;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractService;
use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Configurations\LoggerConfigurations;
use CarloNicora\Minimalism\Services\Logger\Interfaces\LogBuilderInterface;
use CarloNicora\Minimalism\Services\Logger\LogBuilders\ErrorLogBuilder;
use CarloNicora\Minimalism\Services\Logger\LogBuilders\InfoLogBuilder;

class Logger extends AbstractService{

    /** @var LoggerConfigurations  */
    private LoggerConfigurations $configData;

    /** @var ErrorLogBuilder  */
    private ErrorLogBuilder $errorLog;

    /** @var InfoLogBuilder  */
    private InfoLogBuilder $infoLog;

    /**
     * abstractApiCaller constructor.
     * @param LoggerConfigurations $configData
     * @param ServicesFactory $services
     * @throws ServiceNotFoundException
     */
    public function __construct(LoggerConfigurations $configData, ServicesFactory $services) {
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->configData = $configData;

        $this->errorLog = new ErrorLogBuilder($this->services);
        $this->infoLog = new InfoLogBuilder($this->services);
    }

    /**
     * @return LogBuilderInterface
     */
    public function error() : LogBuilderInterface
    {
        return $this->errorLog;
    }

    /**
     * @return LogBuilderInterface|InfoLogBuilder
     */
    public function info() : LogBuilderInterface
    {
        return $this->infoLog;
    }

    /**
     * @return int
     */
    public function getLogLevel(): int
    {
        return $this->configData->getLogLevel();
    }

    /**
     *
     */
    public function setLogLevel(): void
    {
        $this->configData->setLogLevel();
    }
}