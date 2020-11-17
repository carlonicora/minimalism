<?php
namespace CarloNicora\Minimalism\Services\Logger\Configurations;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceConfigurations;
use CarloNicora\Minimalism\Services\Paths\Paths;

class LoggerConfigurations extends AbstractServiceConfigurations {
    public const LOG_LEVEL_NONE=0;
    public const LOG_LEVEL_SLOW=0b1;
    public const LOG_LEVEL_SIMPLE=0b10;
    public const LOG_LEVEL_ALL=0b100;

    /** @var int  */
    private int $logLevel=self::LOG_LEVEL_NONE;

    /** @var array  */
    protected array $dependencies = [
        Paths::class
    ];

    /**
     * LoggerConfigurations constructor.
     */
    public function __construct()
    {
    }

    /**
     *
     */
    public function setLogLevel(): void
    {
        $logLevel = getenv('MINIMALISM_LOG_LEVEL');

        if ($logLevel !== null) {
            $logTypes = explode('+', $logLevel);

            foreach ($logTypes as $logType){
                switch (strtoupper($logType)){
                    case 'SLOW':
                        $this->logLevel += self::LOG_LEVEL_SLOW;
                        break;
                    case 'SIMPLE':
                        $this->logLevel += self::LOG_LEVEL_SIMPLE;
                        break;
                    case 'ALL':
                        $this->logLevel += self::LOG_LEVEL_ALL;
                        break;
                }
            }
        }
    }

    /**
     * @return int
     */
    public function getLogLevel(): int
    {
        return $this->logLevel;
    }
}