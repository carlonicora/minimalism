<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;

abstract class AbstractCliModel extends AbstractModel
{
    public function preRender(): void
    {
    }

    abstract public function run():ResponseInterface;
}