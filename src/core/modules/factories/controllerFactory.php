<?php
namespace carlonicora\minimalism\core\modules\factories;

use carlonicora\minimalism\core\modules\exceptions\prerequisiteException;

class controllerFactory {
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

        $classList = get_declared_classes();
        /** @noinspection PhpIncludeInspection */
        require_once $controllers[0];
        $newClasses = array_values(array_diff_key(get_declared_classes(),$classList));
        return $newClasses[0];
    }
}