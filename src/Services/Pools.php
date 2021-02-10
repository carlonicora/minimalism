<?php
namespace CarloNicora\Minimalism\Services;

use CarloNicora\Minimalism\Interfaces\BuilderInterface;
use CarloNicora\Minimalism\Interfaces\CacheBuilderFactoryInterface;
use CarloNicora\Minimalism\Interfaces\CacheInterface;
use CarloNicora\Minimalism\Interfaces\DataInterface;
use CarloNicora\Minimalism\Interfaces\DataLoaderInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use Exception;

class Pools implements ServiceInterface
{
    /** @var array  */
    private array $loaders=[];

    /** @var ServiceInterface|null  */
    private ?ServiceInterface $defaultService=null;

    /** @var CacheBuilderFactoryInterface|null  */
    private ?CacheBuilderFactoryInterface $cacheFactory=null;

    /** @var BuilderInterface|null  */
    private ?BuilderInterface $builder=null;

    /**
     * Pools constructor.
     * @param DataInterface $data
     * @param CacheInterface|null $cache
     */
    public function __construct(
        protected DataInterface $data,
        protected ?CacheInterface $cache=null,
    )
    {
    }

    /**
     * @param BuilderInterface $builder
     */
    public function setBuilder(BuilderInterface $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * @param ServiceInterface $defaultService
     */
    public function setDefaultService(ServiceInterface $defaultService): void
    {
        $this->defaultService = $defaultService;
    }

    /**
     * @param CacheBuilderFactoryInterface $cacheFactory
     */
    public function setCacheFactory(CacheBuilderFactoryInterface $cacheFactory): void
    {
        $this->cacheFactory = $cacheFactory;
    }

    /**
     * @return BuilderInterface
     */
    public function getBuilder(): BuilderInterface
    {
        return $this->builder;
    }

    /**
     * @return CacheInterface|null
     */
    public function getCache(): ?CacheInterface
    {
        return $this->cache;
    }

    /**
     * @return CacheBuilderFactoryInterface|null
     */
    public function getCacheFactory(): ?CacheBuilderFactoryInterface
    {
        return $this->cacheFactory;
    }

    /**
     * @return DataInterface
     */
    public function getData(): DataInterface
    {
        return $this->data;
    }

    /**
     * @return ServiceInterface|null
     */
    public function getDefaultService(): ?ServiceInterface
    {
        return $this->defaultService;
    }

    /**
     * @param string $className
     * @return DataLoaderInterface
     * @throws Exception
     */
    public function get(string $className): DataLoaderInterface
    {
        if (!array_key_exists($className, $this->loaders)) {
            $this->loaders[$className] = new $className(
                pools: $this,
            );
        }

        return $this->loaders[$className];
    }

    /**
     *
     */
    public function initialise(): void
    {
        $this->loaders = [];
    }

    /**
     *
     */
    public function destroy(): void
    {
        $this->loaders = [];
    }
}