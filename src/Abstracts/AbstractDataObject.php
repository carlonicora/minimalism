<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\Minimalism\Interfaces\DataObjectInterface;

abstract class AbstractDataObject implements DataObjectInterface
{
    /** @var array  */
    private array $originalValues = [];

    /**
     * Context constructor.
     * @param array|null $data
     */
    public function __construct(
        ?array $data=null
    )
    {
        if ($data !== null){
            if (array_key_exists('originalValues', $data)) {
                $this->originalValues = $data['originalValues'];
            }

            $this->import(
                data: $data
            );
        }
    }

    /**
     * @param array $data
     */
    abstract public function import(array $data): void;

    /**
     * @return array
     */
    public function export(
    ): array
    {
        if ($this->originalValues === []){
            return [];
        }

        return [
            'originalValues' => $this->originalValues
        ];
    }
}