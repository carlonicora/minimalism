<?php
namespace CarloNicora\Minimalism\Core\Modules\Factories;

use CarloNicora\Minimalism\Core\Modules\Exceptions\PrerequisiteException;

class ControllerFactory {
    /**
     * @return string
     * @throws PrerequisiteException
     */
    public function loadControllerName() : string {
        $controllers = glob(realpath('./vendor') . '/*/*-module-*/src/controller.php');

        if (count($controllers) === 0) {
            throw new PrerequisiteException('Core module not loaded');
        }

        if (count($controllers) > 1) {
            throw new PrerequisiteException('Multiple Core modules loaded');
        }

        $classList = get_declared_classes();
        /** @noinspection PhpIncludeInspection */
        require_once $controllers[0];
        $newClasses = array_values(array_diff_key(get_declared_classes(),$classList));
        return $newClasses[0];
    }
}