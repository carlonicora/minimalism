<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DataObjectInterface
{
    /**
     * DataObjectInterface constructor.
     * @param array|null $data
     */
    public function __construct(
        ?array $data=null,
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