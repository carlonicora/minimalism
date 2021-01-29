<?php
namespace CarloNicora\Minimalism\Interfaces;

interface TableInterface
{
    /**
     * @return string
     */
    public function getTableName(): string;

    /**
     * @param array $records
     * @param bool $delete
     */
    public function update(array &$records, bool $delete = false): void;

    /**
     * @param array $records
     */
    public function delete(array $records): void;

    /**
     * @param string $fieldName
     * @param $fieldValue
     * @return array
     */
    public function byField(string $fieldName, $fieldValue): array;

    /**
     * @param $id
     * @return array
     */
    public function byId($id): array;

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @param string $joinedTableName
     * @param string $joinedTablePrimaryKeyName
     * @param string $joinedTableForeignKeyName
     * @param int $joinedTablePrimaryKeyValue
     * @return array|null
     */
    public function getFirstLevelJoin(string $joinedTableName, string $joinedTablePrimaryKeyName, string $joinedTableForeignKeyName, int $joinedTablePrimaryKeyValue): ?array;
}