<?php
namespace CarloNicora\Minimalism\Core\Modules\Factories;

use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;

class ControllerFactory {
    /** @var array|null  */
    private ?array $controllers;

    /**
     * ControllerFactory constructor.
     */
    public function __construct()
    {
        $this->controllers = glob(realpath('./vendor') . '/*/*-module-*/src/controller.php');
    }

    /**
     * @return string
     * @throws ConfigurationException
     */
    public function loadControllerName() : string {
        if (count($this->controllers) === 0) {
            throw new ConfigurationException('Core module not loaded', ConfigurationException::ERROR_NO_MODULE_AVAILABLE);
        }

        if (count($this->controllers) > 1) {
            throw new ConfigurationException('Multiple Core modules loaded', ConfigurationException::ERROR_MULITPLE_MODULES_AVAILABLE);
        }

        $classList = get_declared_classes();
        /** @noinspection PhpIncludeInspection */
        require_once $this->controllers[0];
        $newClasses = array_values(array_diff_key(get_declared_classes(),$classList));
        return $newClasses[0];
    }
}