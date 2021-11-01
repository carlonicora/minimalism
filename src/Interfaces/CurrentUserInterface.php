<?php

namespace CarloNicora\Minimalism\Interfaces;

interface CurrentUserInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return int
     */
    public function getEmail(): int;

    /**
     * @return array
     */
    public function getSettings(): array;

    /**
     * @return UserRoleInterface
     */
    public function getRole(): UserRoleInterface;
}