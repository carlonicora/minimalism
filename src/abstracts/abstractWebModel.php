<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

abstract class abstractWebModel extends abstractModel {
    /** @var string */
    protected string $viewName='';

    /**
     * @return array
     */
    public function generateData(): array{
        return [];
    }

    /**
     * @return string
     */
    public function getViewName(): string {
        return $this->viewName;
    }
}