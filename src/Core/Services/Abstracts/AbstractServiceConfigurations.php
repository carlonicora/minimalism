<?php
namespace CarloNicora\Minimalism\Core\Services\Abstracts;

use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceConfigurationsInterface;

abstract class AbstractServiceConfigurations implements ServiceConfigurationsInterface
{
    /**
     * @var array
     */
    protected array $dependencies;

    /**
     * @return array
     */
    final public function getDependencies(): array
    {
        return $this->dependencies ?? [];
    }
}