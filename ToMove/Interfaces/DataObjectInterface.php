<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DataObjectInterface
{
    /**
     * DataObjectInterface constructor.
     * @param array|null $data
     * @param int|null $levelOfChildrenToLoad
     */
    public function __construct(
        ?array $data=null,
        ?int $levelOfChildrenToLoad=0,
    );

    /**
     * @param array $data
     */
    public function import(
        array $data,
    ): void;

    /**
     * @return array
     */
    public function export(
    ): array;
}