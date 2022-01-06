<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DefaultServiceInterface extends ServiceInterface
{
    /**
     * @return string[]
     */
    public function getDelayedServices(): array;

    /**
     * @return string|null
     */
    public function getApplicationUrl(): ?string;

    /**
     * @return string|null
     */
    public function getApiUrl(): ?string;
}