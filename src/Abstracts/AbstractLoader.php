<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\Minimalism\Exceptions\RecordNotFoundException;
use CarloNicora\Minimalism\Interfaces\BuilderInterface;
use CarloNicora\Minimalism\Interfaces\CacheBuilderFactoryInterface;
use CarloNicora\Minimalism\Interfaces\CacheInterface;
use CarloNicora\Minimalism\Interfaces\DataInterface;
use CarloNicora\Minimalism\Interfaces\DataLoaderInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Services\Pools;

abstract class AbstractLoader implements DataLoaderInterface
{
    /** @var DataInterface  */
    protected DataInterface $data;

    /** @var BuilderInterface|null  */
    protected ?BuilderInterface $builder=null;

    /** @var CacheInterface|null  */
    protected ?CacheInterface $cache=null;

    /** @var CacheBuilderFactoryInterface|null  */
    protected ?CacheBuilderFactoryInterface $cacheFactory=null;

    /** @var ServiceInterface|null  */
    protected ?ServiceInterface $defaultService=null;

    /**
     * UsersLoader constructor.
     * @param Pools $pools
     */
    public function __construct(
        protected Pools $pools,
    )
    {
        $this->data = $this->pools->getData();
        $this->builder = $this->pools->getBuilder();

        $this->cache = $this->pools->getCache();
        $this->cacheFactory = $this->pools->getCacheFactory();
        $this->defaultService = $this->pools->getDefaultService();
    }

    /**
     * @param array $response
     * @param string|null $recordType
     * @return array
     * @throws RecordNotFoundException
     */
    protected function returnSingleValue(
        array $response,
        ?string $recordType=null,
    ): array
    {
        if ($response === [] || $response === [[]]){
            throw new RecordNotFoundException(
                $recordType === null
                    ? 'Record Not found'
                    : $recordType . ' not found'
            );
        }

        return $response[0];
    }
}