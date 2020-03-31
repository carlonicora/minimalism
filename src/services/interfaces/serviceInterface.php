<?php
namespace carlonicora\minimalism\services\interfaces;

interface serviceInterface {
    /**
     *
     */
    public function cleanNonPersistentVariables() : void;

    /**
     * @param array $cookies
     */
    public function unserialiseCookies(array $cookies) : void;

    /**
     * @return array
     */
    public function serialiseCookies(): array;
}