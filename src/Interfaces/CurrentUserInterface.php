<?php

namespace CarloNicora\Minimalism\Interfaces;

interface CurrentUserInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return array
     */
    public function getSettings(): array;

    /**
     * @return UserRoleInterface
     */
    public function getRole(): UserRoleInterface;
}