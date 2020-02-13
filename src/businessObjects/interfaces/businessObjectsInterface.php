<?php
namespace carlonicora\minimalism\businessObjects\interfaces;

interface businessObjectsInterface {

    /**
     * @param array $data
     * @return array
     */
    public function fromDbModel(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function toDbModel(array $data): array;

}