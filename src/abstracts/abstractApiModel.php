<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

abstract class abstractApiModel {
    /**
     * @return bool
     */
    abstract public function DELETE(): bool;

    /**
     * @return bool
     */
    abstract public function GET(): bool;

    /**
     * @return bool
     */
    abstract public function POST(): bool;

    /**
     * @return bool
     */
    abstract public function PUT(): bool;
}