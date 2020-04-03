<?php
namespace carlonicora\minimalism\core\services\abstracts;

use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;

abstract class abstractServiceConfigurations implements serviceConfigurationsInterface {
    /**
     * @var array
     */
    protected array $dependencies;

    /**
     * @return array
     */
    final public function getDependencies(): array {
        return $this->dependencies ?? [];
    }
}