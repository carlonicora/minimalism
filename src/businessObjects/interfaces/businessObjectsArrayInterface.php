<?php
namespace carlonicora\minimalism\businessObjects\interfaces;

interface businessObjectsArrayInterface {

    /**
     * @param array $data
     * @return array
     */
    public function fromDbModelsArray(array $data): array;

    /**
     * @param array $data
     * @return array
     */
    public function toDbModelsArray(array $data): array;

}