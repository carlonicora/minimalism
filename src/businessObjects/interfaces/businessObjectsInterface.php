<?php
namespace carlonicora\minimalism\businessObjects\interfaces;

use carlonicora\minimalism\jsonapi\resources\resourceObject;

interface businessObjectsInterface {

    /**
     * @param array $data
     * @return resourceObject
     */
    public function fromDbModel(array $data): resourceObject;

    /**
     * @param array $data
     * @return array
     */
    public function toDbModel(array $data): array;

}