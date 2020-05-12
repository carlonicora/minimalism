<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Models;

abstract class AbstractWebModel extends AbstractModel {
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