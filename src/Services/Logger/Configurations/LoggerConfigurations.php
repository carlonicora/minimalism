<?php
namespace CarloNicora\Minimalism\Services\Logger\Configurations;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceConfigurations;
use CarloNicora\Minimalism\Services\Paths\Paths;

class LoggerConfigurations extends AbstractServiceConfigurations {
    /** @var bool  */
    public bool $saveSystemOnly=false;

    /** @var array  */
    protected array $dependencies = [
        Paths::class
    ];

    public function __construct() {
        if((getenv('MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY'))){
            $this->saveSystemOnly = getenv('MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY') === 'true';
        }
    }
}