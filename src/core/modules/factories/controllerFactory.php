<?php
namespace carlonicora\minimalism\core\modules\factories;

use carlonicora\minimalism\core\modules\exceptions\prerequisiteException;
use carlonicora\minimalism\core\traits\filesystem;

class controllerFactory {
    use filesystem;

    /**
     * @return string
     * @throws prerequisiteException
     */
    public function loadControllerName() : string {
        $controllers = glob(realpath('./vendor') . '/*/*-module-*/src/controller.php');

        if (count($controllers) === 0) {
            throw new prerequisiteException('Core module not loaded');
        }

        if (count($controllers) > 1) {
            throw new prerequisiteException('Multiple core modules loaded');
        }

        return $this->getClassNameFromFile($controllers[0]);
    }
}