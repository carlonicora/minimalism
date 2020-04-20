<?php
namespace carlonicora\minimalism\services\logger;

use carlonicora\minimalism\core\services\abstracts\abstractService;
use carlonicora\minimalism\core\services\exceptions\serviceNotFoundException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;
use carlonicora\minimalism\services\logger\configurations\loggerConfigurations;
use carlonicora\minimalism\services\logger\objects\log;

class logger extends abstractService{
    use traits\logger;

    /** @var loggerConfigurations  */
    private loggerConfigurations $configData;

    /** @var array  */
    private array $events=[];

    /** @var array|bool  */
    private bool $systemEventsOnly=true;

    /**
     * abstractApiCaller constructor.
     * @param serviceConfigurationsInterface $configData
     * @param servicesFactory $services
     * @throws serviceNotFoundException
     */
    public function __construct(serviceConfigurationsInterface $configData, servicesFactory $services) {
        parent::__construct($configData, $services);

        $this->loggerInitialise($services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->configData = $configData;
    }

    /**
     * @param log|null $loggedMessage
     * @param string|null $message
     */
    public function addSystemEvent(log $loggedMessage=null, string $message=null): void{
        if ($loggedMessage === null && $message !== null){
            $loggedMessage = new log($message);
        }

        if ($loggedMessage !== null){
            $this->events[$loggedMessage->time] = $loggedMessage;
        }
    }

    /**
     * @param string $message
     */
    public function addEvent(string $message):void {
        $this->events[] = new log($message);
        $this->systemEventsOnly = false;
    }

    /**
     */
    public function flush(): void {
        if ($this->systemEventsOnly && !$this->configData->saveSystemOnly) {
            return;
        }

        $this->addEvent('request completed');

        $this->write();
    }

    /**
     *
     */
    private function write(): void {
        $log = '';

        $start = 0;
        $previous = 0;

        ksort($this->events);

        /** @var log $log */
        foreach ($this->events as $log){
            if ($previous === 0) {
                $log .= $log->message . ' - ' . date('d.m.Y H:i:s') . PHP_EOL;
                $start = $log->time;
            } else {
                $log .= '    ' . $log->message . ' (' . $this->getDifference($log->time, $previous) . ')'.PHP_EOL;
            }
            $previous = $log->time;
        }
        $log .= '    in ' . $this->getDifference($previous, $start) .PHP_EOL.PHP_EOL;

        $this->loggerWriteTiming($log);
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
}