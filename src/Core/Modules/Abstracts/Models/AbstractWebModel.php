<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Response;

abstract class AbstractWebModel extends AbstractModel
{
    /** @var string */
    protected string $viewName='';

    /**
     * @return string
     */
    public function getViewName(): string
    {
        return $this->viewName;
    }

    /**
     * @return mixed
     */
    abstract public function generateData() : Response;
}