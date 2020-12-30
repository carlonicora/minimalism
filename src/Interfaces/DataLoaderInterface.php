<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DataLoaderInterface
{
    /**
     * DataLoaderInterface constructor.
     * @param DataInterface $dataProvider
     * @param CacheInterface|null $cacheProvider
     */
    public function __construct(
        DataInterface $dataProvider,
        ?CacheInterface $cacheProvider,
    );

    /**
     * @return DataInterface
     */
    public function getDataProvider(): DataInterface;

    /**
     * @return CacheInterface|null
     */
    public function getCacheProvider(): ?CacheInterface;
}