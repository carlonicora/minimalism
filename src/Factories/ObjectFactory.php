<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Abstracts\AbstractFactory;
use CarloNicora\Minimalism\Interfaces\ObjectFactoryInterface;
use CarloNicora\Minimalism\Interfaces\ObjectInterface;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionUnionType;
use RuntimeException;

class ObjectFactory extends AbstractFactory
{
    /** @var array  */
    private array $objectsFactoriesDefinitions=[];

    /** @var bool  */
    private bool $objectsFactoriesDefinitionsUpdated=false;

    /**
     * @param MinimalismFactories $minimalismFactories
     * @throws Exception
     */
    public function __construct(
        MinimalismFactories $minimalismFactories,
    )
    {
        parent::__construct(minimalismFactories: $minimalismFactories);

        if (is_file($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('minimalismObjectsDefinitions.cache'))  && ($cache = file_get_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('minimalismObjectsDefinitions.cache'))) !== false) {
            $this->objectsFactoriesDefinitions = unserialize($cache, [true]);
        }
    }

    /**
     *
     */
    public function __destruct(
    )
    {
        if ($this->objectsFactoriesDefinitionsUpdated) {
            file_put_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('minimalismObjectsDefinitions.cache'), serialize($this->objectsFactoriesDefinitions));
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
    ): ObjectInterface
    {
        if (array_key_exists($className, $this->objectsFactoriesDefinitions) && array_key_exists($this->objectsFactoriesDefinitions[$className], $this->objectsFactoriesDefinitions)) {
            $factoryName = $this->objectsFactoriesDefinitions[$className];
            $factoryConstructMethodParametersDefinitions = $this->objectsFactoriesDefinitions[$factoryName];
        } else {
            $factoryName = null;

            try {
                /** @var ReflectionUnionType $types */
                $types = (new ReflectionClass($className))->getMethod('getObjectFactoryClass')->getReturnType();

                foreach ($types->getTypes() as $type){
                    if ($type->getName() !== 'string'){
                        $factoryName = $type->getName();
                        break;
                    }
                }
            } catch (ReflectionException) {
                $factoryName = null;
            }

            if ($factoryName === null){
                throw new RuntimeException('nope', 500);
            }

            $reflectionMethod = (new ReflectionClass($factoryName))->getMethod('__construct');
            $factoryConstructMethodParametersDefinitions = $this->getMethodParametersDefinition($reflectionMethod);

            $this->objectsFactoriesDefinitions[$className] = $factoryName;
            $this->objectsFactoriesDefinitions[$factoryName] = $factoryConstructMethodParametersDefinitions;

            $this->objectsFactoriesDefinitionsUpdated = true;
        }

        /** @var ObjectFactoryInterface $factory */
        $factoryConstructorParameters = $this->generateMethodParametersValues(
            methodParametersDefinition: $factoryConstructMethodParametersDefinitions,
            parameters: $parameters,
        );

        return (new $factoryName(...$factoryConstructorParameters))->create(
            name: $className,
            parameters: $parameters,
        );
    }
}