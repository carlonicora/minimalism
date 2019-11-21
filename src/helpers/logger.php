<?php
namespace carlonicora\minimalism\helpers;

class logger {
    /** @var array */
    private $events;

    /** @var string */
    private $logFile;

    /** @var string */
    private $errorFile;

    /**
     * logger constructor.
     * @param string $logDirectory
     */
    public function __construct(string $logDirectory) {
        $this->reset();

        $this->logFile = $logDirectory . date('Ymd').'.log';
        $this->errorFile = $logDirectory . 'errors.log';
    }

    /**
     * @param string $errorMessage
     */
    public function addError(string $errorMessage): void{
        $errorLog = date('Y-m-d H:i:s') . ' - ' . $errorMessage . PHP_EOL;
        $errorLog .= json_encode(debug_backtrace());

        error_log($errorLog, 3, $this->errorFile);
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
        $this->write();
        $this->reset();
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