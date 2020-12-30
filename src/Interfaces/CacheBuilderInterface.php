<?php
namespace CarloNicora\Minimalism\Interfaces;

interface CacheBuilderInterface
{
    public const ALL=0;
    public const DATA=1;
    public const JSON=2;

    /**
     * @param int $type
     */
    public function setType(int $type): void;

    /**
     * @return int|string
     */
    public function getCacheIdentifier(): int|string;

    /**
     * @param int|string $identifier
     * @return void
     */
    public function setCacheIdentifier(int|string $identifier): void;
}