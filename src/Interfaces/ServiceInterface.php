<?php
namespace CarloNicora\Minimalism\Interfaces;

interface ServiceInterface
{
    /**
     *
     */
    public function initialise(): void;

    /**
     *
     */
    public function destroy(): void;
}