<?php
namespace CarloNicora\Minimalism\Services\Logger\Interfaces;

interface ErrorsInterface {
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