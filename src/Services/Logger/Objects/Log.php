<?php
namespace CarloNicora\Minimalism\Services\Logger\Objects;

class Log {
    /** @var string  */
    public string $message;

    /** @var float  */
    public float $time;

    public function __construct(string $message) {
        $this->message = $message;
        $this->time = microtime(true);
    }
}