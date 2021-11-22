<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\Minimalism\Exceptions\RecordNotFoundException;
use CarloNicora\Minimalism\Interfaces\BuilderInterface;
use CarloNicora\Minimalism\Interfaces\CacheBuilderFactoryInterface;
use CarloNicora\Minimalism\Interfaces\CacheInterface;
use CarloNicora\Minimalism\Interfaces\DataInterface;
use CarloNicora\Minimalism\Interfaces\DataLoaderInterface;
use CarloNicora\Minimalism\Interfaces\DataObjectInterface;
use CarloNicora\Minimalism\Interfaces\MinimalismObjectInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Services\Pools;
use Exception;

abstract class AbstractLoader implements DataLoaderInterface, MinimalismObjectInterface
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

    /**
     * @param array $recordset
     * @param string $objectType
     * @param int|null $levelOfChildrenToLoad
     * @return DataObjectInterface
     * @throws Exception
     */
    protected function returnSingleObject(
        array $recordset,
        string $objectType,
        ?int $levelOfChildrenToLoad=0,
    ): DataObjectInterface
    {
        if ($recordset === [] || $recordset === [[]]){
            throw new RecordNotFoundException('Record Not found');
        }

        return new $objectType(
            data: $recordset[0],
            levelOfChildrenToLoad: $levelOfChildrenToLoad,
        );
    }

    /**
     * @param array $recordset
     * @param string $objectType
     * @param int|null $levelOfChildrenToLoad
     * @return DataObjectInterface[]
     */
    protected function returnObjectArray(
        array $recordset,
        string $objectType,
        ?int $levelOfChildrenToLoad=0,
    ): array
    {
        $response = [];

        foreach ($recordset ?? [] as $record){
            $response[] = new $objectType(
                data: $record,
                levelOfChildrenToLoad: $levelOfChildrenToLoad,
            );
        }

        return $response;
    }
}