<?php
namespace CarloNicora\Minimalism\Services\Paths\Configurations;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceConfigurations;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceFactoryInterface;
use ReflectionClass;
use ReflectionException;

class PathsConfigurations extends AbstractServiceConfigurations
{
    /**
     * @var array
     */
    private array $servicesPaths = [];

    /**
     * @var array
     */
    private array $serviceFactories = [];

    /**
     * @var array
     */
    private array $plugins=[];

    /**
     * mailingConfigurations constructor.
     */
    public function __construct()
    {
        $this->initialiseServiceFactories();
        $this->initialiseServicesPaths();
    }

    /**
     *
     */
    private function initialiseServicesPaths(): void
    {
        foreach ($this->plugins ?? [] as $plugin) {
            $this->servicesPaths[] = dirname($plugin, 2);
        }
    }

    /**
     *
     */
    private function initialiseServiceFactories(): void
    {
        $this->plugins = glob(realpath('./vendor') . '/*/*/src/Factories/ServiceFactory.php');
        $builtIn = glob(realpath('./vendor') . '/*/*/src/Services/*/Factories/ServiceFactory.php');
        $internal = glob(realpath('./src') . '/Services/*/Factories/ServiceFactory.php');
        $microservice = glob(realpath('./src') . '/Factories/ServiceFactory.php');

        $files = array_unique(array_merge($this->plugins, $builtIn, $internal, $microservice));

        foreach ($files as $fileName) {
            /** @noinspection PhpIncludeInspection */
            require_once $fileName;
        }

        $classes = get_declared_classes();
        foreach ($classes as $singleClass) {
            try {
                $reflect = new ReflectionClass($singleClass);
                if ($reflect->implementsInterface(ServiceFactoryInterface::class) && !$reflect->isAbstract()) {
                    $this->serviceFactories[] = $singleClass;
                }
            } catch (ReflectionException $e) {
            }
        }
    }

    /**
     * @return array
     */
    public function getServiceFactories(): array
    {
        return $this->serviceFactories;
    }

    /**
     * @return array
     */
    public function getServicesPaths(): array
    {
        return $this->servicesPaths;
    }
}