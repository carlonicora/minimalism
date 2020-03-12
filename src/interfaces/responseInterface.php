<?php
namespace carlonicora\minimalism\interfaces;

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
}