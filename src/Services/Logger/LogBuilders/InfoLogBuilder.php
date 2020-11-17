<?php
namespace CarloNicora\Minimalism\Services\Logger\LogBuilders;

use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;
use CarloNicora\Minimalism\Core\Events\MinimalismInfoEvents;
use CarloNicora\Minimalism\Services\Logger\Abstracts\AbstractLogBuilder;
use CarloNicora\Minimalism\Services\Logger\Configurations\LoggerConfigurations;
use Exception;

class InfoLogBuilder extends AbstractLogBuilder
{
    /** @var array|EventInterface[]  */
    private array $events=[];

    /**
     * @param EventInterface $logMessage
     * @return EventInterface
     */
    public function log(EventInterface $logMessage): EventInterface
    {
        $this->events[] = $logMessage;

        return $logMessage;
    }

    /**
     * @return array|EventInterface[]
     */
    public function getEvents() : array
    {
        return $this->events;
    }

    /**
     *
     */
    public function clearEvents() : void
    {
        $this->events = [];
    }

    /**
     * @param array $events
     */
    public function resetEvents(array $events) : void{
        $this->events = $events;
    }

    /**
     * @param array $events
     */
    public function setEvents(array $events) : void
    {
        $this->events = array_merge($this->events, $events);
    }

    /**
     * @param string $title
     */
    public function setEventsTitle(string $title): void
    {
        if (count($this->events) > 0) {
            $this->events[0]['details'] = $title;
        }
    }

    /**
     * @param EventInterface $previous
     * @param EventInterface $next
     * @return int
     */
    private function compare(EventInterface $previous, EventInterface $next) : int{
        return $previous->getTime() <= $next->getTime() ? 0 : 1;
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
     * @throws Exception
     */
    public function flush() : void
    {
        if (count($this->events) > 0) {
            $this->events[] = MinimalismInfoEvents::END();

            $start = 0;
            $previous = 0;

            usort($this->events, [$this, 'compare']);

            $info = [];

            $logMessage = new MinimalismInfoEvents(0, null, 'Request');
            $event = json_decode($logMessage->generateMessage(), true, 512, JSON_THROW_ON_ERROR);
            $info[] = $event;

            foreach ($this->events as $logMessage) {
                $event = json_decode($logMessage->generateMessage(), true, 512, JSON_THROW_ON_ERROR);

                if ($previous === 0) {
                    $start = $logMessage->getTime();
                } else {
                    $event['duration'] = $this->getDifference($logMessage->getTime(), $previous);
                }

                $info[] = $event;
                $previous = $logMessage->getTime();
            }

            $info[0]['duration'] = $this->getDifference($previous, $start);

            if (
                ($this->services->logger()->getLogLevel() & LoggerConfigurations::LOG_LEVEL_ALL) > 0
                ||
                (
                    ($this->services->logger()->getLogLevel() & LoggerConfigurations::LOG_LEVEL_SLOW) > 0
                    &&
                    $info[0]['duration'] > 5
                )
            ) {
                $infoMessage = json_encode($info, JSON_THROW_ON_ERROR) . PHP_EOL;

                $infoFile = $this->logDirectory . 'system.log';

                /** @noinspection ForgottenDebugOutputInspection */
                error_log($infoMessage, 3, $infoFile);

                if (($this->services->logger()->getLogLevel() & LoggerConfigurations::LOG_LEVEL_SIMPLE) > 0){
                    $recap = array_shift($info);
                    $simplerInfoMessage = '[' . $recap['time'] . ']'
                        .  ' ' . $recap['details']
                        . ' duration ' . $recap['duration'] . PHP_EOL;
                    foreach ($info as $infoPosition=>$infoElement){
                        $simplerInfoMessage .= '   '
                            . $infoPosition . '. ' . $infoElement['service']
                            . ' - '
                            . $infoElement['details']
                            . ' [id:' . $infoElement['id'] . ']'
                            . (array_key_exists('duration', $infoElement) ? ' (' . $infoElement['duration'] . ')' : '')
                            . PHP_EOL;
                    }

                    $simplerInfoMessage .= PHP_EOL;

                    $simplerInfoFile = $this->logDirectory . 'system.simple.log';

                    /** @noinspection ForgottenDebugOutputInspection */
                    error_log($simplerInfoMessage, 3, $simplerInfoFile);
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    public function __destruct()
    {
        $this->flush();
        parent::__destruct();
    }
}