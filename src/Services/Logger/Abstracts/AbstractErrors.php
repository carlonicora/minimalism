<?php
namespace CarloNicora\Minimalism\Services\Logger\Abstracts;

use CarloNicora\Minimalism\Services\Logger\Interfaces\ErrorsInterface;
use ReflectionClass;
use ReflectionException;

abstract class AbstractErrors implements ErrorsInterface {
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