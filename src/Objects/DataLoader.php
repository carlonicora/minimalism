<?php
namespace CarloNicora\Minimalism\Objects;

use CarloNicora\Minimalism\Interfaces\CacheInterface;
use CarloNicora\Minimalism\Interfaces\DataInterface;
use CarloNicora\Minimalism\Interfaces\DataLoaderInterface;

class DataLoader implements DataLoaderInterface
{
    /**
     * DataLoader constructor.
     * @param DataInterface $dataProvider
     * @param CacheInterface|null $cacheProvider
     */
    public function __construct(
        private DataInterface $dataProvider,
        private ?CacheInterface $cacheProvider=null
    )
    {
    }

    /**
     * @return DataInterface
     */
    public function getDataProvider(): DataInterface
    {
        return $this->dataProvider;
    }

    /**
     * @return CacheInterface|null
     */
    public function getCacheProvider(): ?CacheInterface
    {
        return $this->cacheProvider;
    }
}