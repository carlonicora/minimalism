<?php
namespace CarloNicora\Minimalism\Factories;

class TimerFactory
{
    /** @var float  */
    private static float $start;

    /**
     * @return void
     */
    public static function start(
    ): void
    {
        static::$start = round(microtime(true) * 1000, 2);
    }

    /**
     * @return float
     */
    public static function elapse(
    ): float
    {
        $end = round(microtime(true) * 1000, 2);

        return round($end - static::$start, 2);
    }
}