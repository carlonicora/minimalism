<?php declare(strict_types=1);

namespace carlonicora\minimalism\abstracts;

abstract class abstractWebModel extends abstractModel {

    /** @var string */
    protected $viewName;
    
    /**
     * @return array
     */
    abstract public function generateData(): array;

    /**
     * @return string
     */
    public function getViewName(): string {
        $returnValue = $this->viewName;

        if (!isset($returnValue)) {
            $returnValue = '';
        }

        return $returnValue;
    }
}