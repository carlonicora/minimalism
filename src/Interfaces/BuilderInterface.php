<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Services\Pools;

interface BuilderInterface
{
    /**
     * JsonApiBuilderFactory constructor.
     * @param DataInterface $data
     * @param Pools $pools
     * @param EncrypterInterface $encrypter
     * @param Path $path
     * @param CacheInterface|null $cache
     */
    public function __construct(
        DataInterface $data,
        Pools $pools,
        EncrypterInterface $encrypter,
        Path $path,
        ?CacheInterface $cache,
    );

    /**
     * @param string $resourceTransformerClass
     * @param DataFunctionInterface $function
     * @param int $relationshipLevel
     * @param array $additionalRelationshipData
     * @return array
     */
    public function build(
        string $resourceTransformerClass,
        DataFunctionInterface $function,
        int $relationshipLevel=1,
        array $additionalRelationshipData=[]
    ): array;

    /**
     * @param string $resourceTransformerClass
     * @param array $data
     * @param int $relationshipLevel
     * @param array $additionalRelationshipData
     * @return array
     */
    public function buildByData(
        string $resourceTransformerClass,
        array $data,
        int $relationshipLevel=1,
        array $additionalRelationshipData=[]
    ): array;
}