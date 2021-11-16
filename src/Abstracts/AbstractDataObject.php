<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\Minimalism\Interfaces\DataObjectInterface;
use RuntimeException;

abstract class AbstractDataObject implements DataObjectInterface
{
    public const TYPE_GENERIC=0;
    public const TYPE_DATE=1;

    /** @var array  */
    private array $originalValues = [];

    /**
     * Context constructor.
     * @param array|null $data
     * @param int|null $levelOfChildrenToLoad
     */
    public function __construct(
        ?array $data=null,
        protected ?int $levelOfChildrenToLoad=0,
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

    /**
     * @param array $data
     * @param string $fieldName
     * @param bool $isRequired
     * @param mixed|null $defaultValue
     * @param int $type
     * @return mixed
     */
    protected function importField(
        array $data,
        string $fieldName,
        bool $isRequired=false,
        mixed $defaultValue=null,
        int $type=0,
    ): mixed
    {
        if (!array_key_exists($fieldName, $data) || $data[$fieldName] === null){
            if ($isRequired) {
                throw new RuntimeException($fieldName . ' missing', 412);
            }
            return $defaultValue;
        }

        return match ($type){
            self::TYPE_GENERIC => $data[$fieldName],
            self::TYPE_DATE => strtotime($data[$fieldName]),
        };
    }

    /**
     * @param string $fieldName
     * @param mixed $value
     * @param bool $isRequired
     * @return mixed
     */
    protected function setInitialFieldValue(
        string $fieldName,
        mixed $value,
        bool $isRequired=false,
    ): mixed
    {
        if ($isRequired && $value === null){
            throw new RuntimeException($fieldName . ' missing during initialisation', 412);
        }

        return $value;
    }
}