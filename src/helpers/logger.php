<?php /** @noinspection ForgottenDebugOutputInspection */

namespace carlonicora\minimalism\helpers;

class logger {
    /** @var array */
    private array $events;

    /** @var string */
    private string $logFile;

    /** @var string */
    private string $logFileCondensed;

    /** @var string */
    private string $errorFile;

    /** @var string */
    private string $queryFile;

    /** @var bool */
    private bool $logEvents;

    /** @var bool */
    private bool $logQueries;

    /**
     * logger constructor.
     * @param string $logDirectory
     */
    public function __construct(string $logDirectory) {
        $this->reset();

        $this->logFile = $logDirectory . date('Ymd').'.log';
        $this->logFileCondensed = $logDirectory . date('Ymd').'.condensed.log';
        $this->errorFile = $logDirectory . 'errors.log';
        $this->queryFile = $logDirectory . 'queries.log';

        $this->logEvents = false;
        $this->logQueries = false;
    }

    /**
     * @param bool $logEvents
     */
    public function setLogEvents(bool $logEvents): void {
        $this->logEvents = $logEvents;
    }

    /**
     * @param bool $logQueries
     */
    public function setLogQueries(bool $logQueries): void {
        $this->logQueries = $logQueries;
    }

    /**
     * @param string $errorMessage
     */
    public function addError(string $errorMessage): void{
        $errorLog = date('Y-m-d H:i:s') . ' - ' . $errorMessage . PHP_EOL;

        error_log($errorLog, 3, $this->errorFile);
    }

    /**
     * @param string $sql
     * @param array $parameters
     */
    public function addQuery(string $sql, array $parameters = null): void{
        if (!$this->logQueries) {
            return;
        }

        $errorLog = date('Y-m-d H:i:s') . ',' . $sql . ',';
        if (!empty($parameters)){
            $errorLog .= json_encode($parameters, JSON_THROW_ON_ERROR, 512);
        }
        $errorLog .= PHP_EOL;
        error_log($errorLog, 3, $this->queryFile);
    }

    /**
     *
     */
    public function reset(): void {
        $this->events = [];
    }

    /**
     * @param string $event
     */
    public function addEvent(string $event): void {
        $this->events[$event] = microtime(true);
    }

    public function flush(string $event = null): void {
        if (!empty($event)){
            $this->addEvent($event);
        }
        if ($this->logEvents) {
            $this->write();
            $this->writeCondensed();
        }
        $this->reset();
    }

    public function writeCondensed(): void {
        $event = array_key_first($this->events);
        $end = array_key_last($this->events);

        $log = $event . ' - ' . $this->getDifference($this->events[$end], $this->events[$event]) . PHP_EOL;

        error_log($log, 3, $this->logFileCondensed);
    }

    public function write(): void {
        $log = '';

        $start = 0;
        $previous = 0;
        foreach ($this->events as $name=>$time){
            if ($previous === 0) {
                $log .= $name . ' - ' . date('d.m.Y H:i:s') . PHP_EOL;
                $start = $time;
            } else {
                $log .= '    ' . $name . ' (' . $this->getDifference($time, $previous) . ')'.PHP_EOL;
            }
            $previous = $time;
        }
        $log .= '    in ' . $this->getDifference($previous, $start) .PHP_EOL.PHP_EOL;

        error_log($log, 3, $this->logFile);
    }

    private function getDifference($end, $start): string {
        $intResponse = (int)(($end-$start) * 10000);

        return $intResponse/10000 . ' seconds';
    }
}