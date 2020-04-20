<?php
namespace carlonicora\minimalism\services\logger\configurations;

use carlonicora\minimalism\core\services\abstracts\abstractServiceConfigurations;
use carlonicora\minimalism\services\paths\paths;

class loggerConfigurations extends abstractServiceConfigurations {
    /** @var bool  */
    public bool $saveSystemOnly=false;

    /** @var array  */
    protected array $dependencies = [
        paths::class
    ];

    public function __construct() {
        if((getenv('MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY'))){
            $this->saveSystemOnly = getenv('MINIMALISM_SERVICE_LOGGER_SAVE_SYSTEM_ONLY') === 'true';
        }
    }
}