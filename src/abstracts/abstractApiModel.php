<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

abstract class abstractApiModel extends abstractModel {
    /**
     * @return array
     */
    abstract public function DELETE(): array;

    /**
     * @return array
     */
    abstract public function GET(): array;

    /**
     * @return array
     */
    abstract public function POST(): array;

    /**
     * @return array
     */
    abstract public function PUT(): array;
}