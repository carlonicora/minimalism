<?php
namespace carlonicora\minimalism\core\modules\abstracts\controllers;

abstract class abstractWebController extends abstractController {
    /**
     *
     */
    public function preRender(): void {
        $this->initialiseView();
    }

    /**
     *
     */
    abstract protected function initialiseView(): void;
}