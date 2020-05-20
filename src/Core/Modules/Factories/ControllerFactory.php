<?php
namespace CarloNicora\Minimalism\Core\Modules\Factories;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;

class ControllerFactory
{
    /** @var ServicesFactory  */
    private ServicesFactory $services;

    /** @var array|null  */
    private ?array $controllers;

    /**
     * ControllerFactory constructor.
     * @param ServicesFactory $services
     */
    public function __construct(ServicesFactory $services)
    {
        $this->services = $services;
        $this->controllers = glob(realpath('./vendor') . '/*/*-module-*/src/Controller.php');
    }

    /**
     * @return ControllerInterface
     * @throws ConfigurationException
     */
    public function loadController() : ControllerInterface
    {
        if (count($this->controllers) === 0) {
            throw new ConfigurationException('minimalism', 'Core module not loaded', ConfigurationException::ERROR_NO_MODULE_AVAILABLE);
        }

        if (count($this->controllers) > 1) {
            throw new ConfigurationException('minimalism', 'Multiple Core modules loaded', ConfigurationException::ERROR_MULITPLE_MODULES_AVAILABLE);
        }

        $classList = get_declared_classes();
        /** @noinspection PhpIncludeInspection */
        require_once $this->controllers[0];
        $newClasses = array_values(array_diff_key(get_declared_classes(),$classList));

        if (count($newClasses) > 0) {
            $controllerClass = $newClasses[0];
        } else {
            $controllerClass = $this->getNamespace($this->controllers[0])
                . '\\'
                . $this->getClassname($this->controllers[0]);
        }

        /** @var ControllerInterface $response */
        $response = new $controllerClass($this->services);

        return $response;
    }

    /**
     * @param $filename
     * @return string
     */
    private function getNamespace($filename) : string
    {
        $lines = file($filename);
        $array = preg_grep('/^namespace /', $lines);
        $namespaceLine = array_shift($array);
        $match = array();
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);
        return array_pop($match);
    }

    /**
     * @param $filename
     * @return mixed|string
     */
    private function getClassname($filename)
    {
        $directoriesAndFilename = explode('/', $filename);
        $filename = array_pop($directoriesAndFilename);
        $nameAndExtension = explode('.', $filename);
        return array_shift($nameAndExtension);
    }
}