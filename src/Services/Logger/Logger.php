<?php
namespace CarloNicora\Minimalism\Services\Logger;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractService;
use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceConfigurationsInterface;
use CarloNicora\Minimalism\Services\Logger\configurations\LoggerConfigurations;
use CarloNicora\Minimalism\Services\Logger\Objects\Log;

class Logger extends AbstractService{
    use traits\loggerTrait;

    /** @var LoggerConfigurations  */
    private LoggerConfigurations $configData;

    /** @var array  */
    private array $events=[];

    /** @var array|bool  */
    private bool $systemEventsOnly=true;

    /**
     * abstractApiCaller constructor.
     * @param ServiceConfigurationsInterface $configData
     * @param ServicesFactory $services
     * @throws ServiceNotFoundException
     */
    public function __construct(ServiceConfigurationsInterface $configData, ServicesFactory $services) {
        parent::__construct($configData, $services);

        $this->loggerInitialise($services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->configData = $configData;
    }

    /**
     * @param Log|null $loggedMessage
     * @param string|null $message
     */
    public function addSystemEvent(Log $loggedMessage=null, string $message=null): void{
        if ($loggedMessage === null && $message !== null){
            $loggedMessage = new Log($message);
        }

        if ($loggedMessage !== null){
            $this->events[] = $loggedMessage;
        }
    }

    /**
     * @param string $message
     */
    public function addEvent(string $message):void {
        $this->events[] = new Log($message);
        $this->systemEventsOnly = false;
    }

    public function flush(): void {
        if ($this->systemEventsOnly && !$this->configData->saveSystemOnly) {
            $this->events = [];
            return;
        }

        $this->addEvent('Request completed');

        $this->write();
    }

    /**
     * @param Log $previous
     * @param Log $next
     * @return int
     */
    private function compare(Log $previous, Log $next) : int{
        return $previous->time <= $next->time ? 0 : 1;
    }

    /**
     *
     */
    private function write(): void {
        $message = '';

        $start = 0;
        $previous = 0;

        usort($this->events, [$this, 'compare']);

        /** @var Log $log */
        foreach ($this->events as $log){
            if ($previous === 0) {
                $message .= $log->message . ' - ' . date('d.m.Y H:i:s') . PHP_EOL;
                $start = $log->time;
            } else {
                $message .= '    ' . $log->message . ' (' . $this->getDifference($log->time, $previous) . ')'.PHP_EOL;
            }
            $previous = $log->time;
        }
        $message .= '    in ' . $this->getDifference($previous, $start) .PHP_EOL.PHP_EOL;

        $this->loggerWriteTiming($message);

        $this->events = [];
    }


    /**
     * @param $end
     * @param $start
     * @return string
     */
    private function getDifference($end, $start): string {
        $intResponse = (int)(($end-$start) * 10000);

        return $intResponse/10000 . ' seconds';
    }

    /**
     * @param ServicesFactory $services
     * @throws ServiceNotFoundException
     */
    public function initialiseStatics(ServicesFactory $services): void {
        $this->loggerInitialise($services);
        $this->events = [];
    }
}