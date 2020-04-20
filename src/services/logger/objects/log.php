<?php
namespace carlonicora\minimalism\services\logger\objects;

class log {
    /** @var string  */
    public string $message;

    /** @var float  */
    public float $time;

    public function __construct(string $message) {
        $this->message = $message;
        $this->time = microtime(true);
    }
}