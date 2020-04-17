<?php
namespace carlonicora\minimalism\services\logger\interfaces;

interface errorsInterface {
    /**
     * @return array
     */
    public function getErrorList() : array;

    /**
     * @param int $errorId
     * @return string
     */
    public function getErrorDescription(int $errorId) : string;
}