<?php
namespace carlonicora\minimalism\businessObjects\interfaces;

use carlonicora\minimalism\interfaces\responseInterface;

interface businessObjectsInterface {

    /**
     * @param array $data
     * @return responseInterface
     */
    public function fromDbModel(array $data): responseInterface;

    /**
     * @param array $data
     * @return responseInterface
     */
    public function toDbModel(array $data): responseInterface;

}