<?php
namespace carlonicora\minimalism\controllers;

use carlonicora\minimalism\abstracts\abstractController;

class cliController extends abstractController {
    /**
     * @return string
     */
    public function render(): string {
        $error = $this->model->preRender();
        if ($error !== null){
            return $error->toJson();
        }

        $this->model->run();

        return '';
    }

    /**
     *
     */
    protected function initialiseParameters(): void {
        if (isset($_SERVER['argv'][1]) && !isset($_SERVER['argv'][2])){
            $this->parameterValues = json_decode($_SERVER['argv'][1], true, 512, JSON_THROW_ON_ERROR);
        } else if (count($_SERVER['argv']) > 1){
            for ($argumentCount = 1, $argumentCountMax = count($_SERVER['argv']); $argumentCount < $argumentCountMax; $argumentCount += 2){
                $this->parameterValues[substr($_SERVER['argv'][$argumentCount], 1)] = $_SERVER['argv'][$argumentCount + 1];
            }
        }
    }
}