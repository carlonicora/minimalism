<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Abstracts\AbstractFactory;
use CarloNicora\Minimalism\Interfaces\ObjectInterface;
use CarloNicora\Minimalism\Interfaces\SimpleObjectInterface;
use CarloNicora\Minimalism\Objects\ModelParameters;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionUnionType;
use RuntimeException;

class ObjectFactory extends AbstractFactory
{
    /** @var array  */
    private array $objectsDefinitions=[];

    /** @var array  */
    private array $objectsFactoriesDefinitions=[];

    /** @var bool  */
    private bool $objectUpdated=false;

    /**
     *
     */
    public function initialiseFactory(
    ): void
    {
        if (
            is_file($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('objectsFactoriesDefinitions.cache'))
            && ($cache = file_get_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('objectsFactoriesDefinitions.cache'))) !== false
        ) {
            $this->objectsFactoriesDefinitions = unserialize($cache, [true]);
        }

        if (
            is_file($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('objectsDefinitions.cache'))
            && ($cache = file_get_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('objectsDefinitions.cache'))) !== false
        ) {
            $this->objectsDefinitions = unserialize($cache, [true]);
        }
    }

    /**
     *
     */
    public function __destruct(
    )
    {
        if ($this->objectUpdated) {
            file_put_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('objectsFactoriesDefinitions.cache'), serialize($this->objectsFactoriesDefinitions));
            file_put_contents($this->minimalismFactories->getServiceFactory()->getPath()->getCacheFile('objectsDefinitions.cache'), serialize($this->objectsDefinitions));
        }
    }

    /**
     * @template InstanceOfType
     * @param class-string<InstanceOfType> $className
     * @param string|null $name
     * @param ModelParameters|null $parameters
     * @return InstanceOfType|null
     * @throws Exception
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     */
    public function create(
        string $className,
        ?string $name=null,
        ?ModelParameters $parameters=null,
    ): mixed
    {
        if (array_key_exists($className, $this->objectsDefinitions)){
            $isSimpleObject = !array_key_exists($className, $this->objectsFactoriesDefinitions);
        } else {
            $isSimpleObject = (new ReflectionClass($className))->implementsInterface(SimpleObjectInterface::class);
        }

        if ($isSimpleObject){
            $response = $this->createSimpleObject(
                className: $className,
                parameters: $parameters,
            );
        } else {
            $response = $this->createComplexObject(
                className: $className,
                name:$name,
                parameters: $parameters,
            );
        }

        return $response;
    }

    /**
     * @param string $className
     * @param string|null $name
     * @param ModelParameters|null $parameters
     * @return ObjectInterface|null
     * @throws Exception
     */
    protected function createComplexObject(
        string $className,
        ?string $name,
        ?ModelParameters $parameters,
    ): ?ObjectInterface
    {
        if (array_key_exists($className, $this->objectsFactoriesDefinitions)){
            $factoryName = $this->objectsFactoriesDefinitions[$className]['factoryName'];
            $factoryConstructMethodParametersDefinitions = $this->objectsFactoriesDefinitions[$className]['coonstructMethodParameters'];
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
            }

            if ($factoryName === null){
                throw new RuntimeException('nope', 500);
            }

            $reflectionMethod = (new ReflectionClass($factoryName))->getMethod('__construct');
            $factoryConstructMethodParametersDefinitions = $this->getMethodParametersDefinition($reflectionMethod);

            $this->objectsFactoriesDefinitions[$className] = [
                'factoryName' => $factoryName,
                'coonstructMethodParameters' => $factoryConstructMethodParametersDefinitions,
            ];

            $this->objectUpdated = true;
        }

        $factoryConstructorParameters = $this->generateMethodParametersValues(
            methodParametersDefinition: $factoryConstructMethodParametersDefinitions,
            parameters: $parameters,
        );

        return (new $factoryName(...$factoryConstructorParameters))->create(
            className: $className,
            parameterName: $name,
            parameters: $parameters,
        );
    }

    /**
     * @param string $className
     * @param ModelParameters|null $parameters
     * @return SimpleObjectInterface|ObjectInterface
     * @throws Exception
     */
    public function createSimpleObject(
        string $className,
        ?ModelParameters $parameters=null,
    ): SimpleObjectInterface|ObjectInterface
    {
        if (array_key_exists($className, $this->objectsDefinitions)) {
            $objectConstructorParametersDefinitions = $this->objectsDefinitions[$className];
        } else {
            $reflectionMethod = (new ReflectionClass($className))->getMethod('__construct');
            $objectConstructorParametersDefinitions = $this->getMethodParametersDefinition($reflectionMethod);

            $this->objectsDefinitions[$className] = $objectConstructorParametersDefinitions;

            $this->objectUpdated = true;
        }

        $classConstructorParameters = $this->generateMethodParametersValues(
            methodParametersDefinition: $objectConstructorParametersDefinitions,
            parameters: $parameters,
        );

        return new $className(...$classConstructorParameters);
    }
}