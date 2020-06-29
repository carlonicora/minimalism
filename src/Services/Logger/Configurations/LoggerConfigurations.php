<?php
namespace CarloNicora\Minimalism\Services\Logger\Configurations;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceConfigurations;
use CarloNicora\Minimalism\Services\Paths\Paths;

class LoggerConfigurations extends AbstractServiceConfigurations {
    /** @var array  */
    protected array $dependencies = [
        Paths::class
    ];

    public function __construct() {
    }


}