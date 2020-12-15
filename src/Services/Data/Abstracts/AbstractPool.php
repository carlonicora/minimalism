<?php
namespace CarloNicora\Minimalism\Services\Data\Abstracts;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;

abstract class AbstractPool
{
    /** @var ServicesFactory  */
    protected ServicesFactory $services;

    /** @var array  */
    protected array $pool=[];

    /**
     * UsersLoader constructor.
     * @param ServicesFactory $services
     */
    public function __construct(ServicesFactory $services)
    {
        $this->services = $services;
    }

    /**
     * @param string $className
     * @return mixed
     * @throws Exception
     */
    protected function loadPoolElement(string $className)
    {
        if (!array_key_exists($className, $this->pool)) {
            $this->pool[$className] = new $className($this->services);
        }
        return $this->pool[$className];
    }
}