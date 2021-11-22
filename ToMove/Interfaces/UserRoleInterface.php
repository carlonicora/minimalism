<?php

namespace CarloNicora\Minimalism\Interfaces;

interface UserRoleInterface
{
    /**
     * @return bool
     */
    public function isVisitor(): bool;
}