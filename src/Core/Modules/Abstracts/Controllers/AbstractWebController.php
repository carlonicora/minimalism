<?php
namespace CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers;

abstract class AbstractWebController extends AbstractController
{
    /**
     *
     */
    public function preRender(): void
    {
        $this->initialiseView();
    }

    /**
     *
     */
    abstract protected function initialiseView(): void;
}