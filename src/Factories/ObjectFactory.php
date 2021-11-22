<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Abstracts\AbstractFactory;
use CarloNicora\Minimalism\Interfaces\ObjectInterface;
use Exception;
use ReflectionClass;

class ObjectFactory extends AbstractFactory
{
    /** @var array  */
    private array $objectsDefinitions=[];

    /** @var bool  */
    private bool $objectsDefinitionsUpdated=false;

    /**
     * @param ServiceFactory $serviceFactory
     * @throws Exception
     */
    public function __construct(
        ServiceFactory $serviceFactory,
    )
    {
        parent::__construct(serviceFactory: $serviceFactory);

        if (is_file($this->serviceFactory->getPath()->getCacheFile('minimalismObjectsDefinitions.cache'))  && ($cache = file_get_contents($this->serviceFactory->getPath()->getCacheFile('minimalismObjectsDefinitions.cache'))) !== false) {
            $this->objectsDefinitions = unserialize($cache, [true]);
        }
    }

    /**
     *
     */
    public function __destruct(
    )
    {
        if ($this->objectsDefinitionsUpdated) {
            file_put_contents($this->serviceFactory->getPath()->getCacheFile('minimalismObjectsDefinitions.cache'), serialize($this->objectsDefinitions));
        }
    }

    /**
     * @param string $className
     * @param array $parameters
     * @return ObjectInterface
     * @throws Exception
     */
    public function create(
        string $className,
        array $parameters=[],
    ): mixed
    {
        $methodParametersDefinitions = $this->getMethodDefinitions(
            className: $className,
        );

        return $this->generateObject(
            className: $className,
            methodParametersDefinition: $methodParametersDefinitions,
            parameters: $parameters,
        );
    }

    /**
     * @param string $className
     * @return array
     * @throws Exception
     */
    private function getMethodDefinitions(
        string $className,
    ): array
    {
        if (!array_key_exists($className, $this->objectsDefinitions)) {
            $this->objectsDefinitionsUpdated = true;

            $this->objectsDefinitions[$className] = $this->getMethodParametersDefinition(
                (new ReflectionClass($className))->getMethod('__construct')
            );
        }

        return $this->objectsDefinitions[$className];
    }
}