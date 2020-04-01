<?php
namespace carlonicora\minimalism\core\jsonapi\interfaces;

interface responseInterface {
    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return string
     */
    public function generateText() : string;

    /**
     * @return string
     */
    public static function generateProtocol() : string;

    /**
     * @return string
     */
    public function toJson() : string;

    /**
     * @return array
     */
    public function toArray() : array;
}