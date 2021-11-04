<?php
namespace CarloNicora\Minimalism\Interfaces;

interface DefaultServiceInterface
{
    /**
     * @return array
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