<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;

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
     *
     */
    public function preRender(): void
    {
    }

    /**
     * @return mixed
     */
    abstract public function generateData() : ResponseInterface;
}