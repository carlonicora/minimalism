<?php
namespace carlonicora\minimalism\services\logger\abstracts;

use carlonicora\minimalism\services\logger\interfaces\errorsInterface;
use ReflectionClass;
use ReflectionException;

abstract class abstractErrors implements errorsInterface {
    /**
     * @return array
     */
    public function getErrorList(): array {
        try {
            $reflect = new ReflectionClass(get_class($this));
            return $reflect->getConstants();
        } catch (ReflectionException $e) {
            return [];
        }
    }

    /**
     * @param int $errorId
     * @return string
     */
    public function getErrorDescription(int $errorId): string{
        return '';
    }
}