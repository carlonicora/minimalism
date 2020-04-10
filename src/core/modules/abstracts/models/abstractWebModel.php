<?php
namespace carlonicora\minimalism\core\modules\abstracts\models;

abstract class abstractWebModel extends abstractModel {
    /** @var string */
    protected string $viewName='';

    /**
     * @return string
     */
    public function getViewName(): string {
        return $this->viewName;
    }

    /**
     * @return mixed
     */
    abstract public function generateData();
}