<?php
namespace CarloNicora\Minimalism\Core\Modules\Factories;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Events\MinimalismInfoEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;
use function class_exists;

class ControllerFactory
{
    /** @var ServicesFactory  */
    private ServicesFactory $services;

    /**
     * ControllerFactory constructor.
     * @param ServicesFactory $services
     */
    public function __construct(ServicesFactory $services)
    {
        $this->services = $services;
    }

    /**
     * @param string|null $controllerClassName
     * @return ControllerInterface
     * @throws Exception
     */
    public function loadController(?string $controllerClassName) : ControllerInterface
    {
        if ($controllerClassName === null){
            $this->services->logger()->error()->log(
                MinimalismErrorEvents::CONTROLLER_NOT_DEFINED()
            )->throw(ConfigurationException::class, 'Controller not set up');
        }

        if (!class_exists($controllerClassName)) {
            $this->services->logger()->error()->log(
                MinimalismErrorEvents::GENERIC_ERROR()
            )->throw(ConfigurationException::class, 'Controller class does not exist');
        }

        /** @var ControllerInterface $response */
        $response = new $controllerClassName($this->services);
        $response->setSecurityInterface($this->services->getSecurityInterface());
        $response->setEncrypterInterface($this->services->getEncrypterInterface());

        $this->services->logger()->info()->log(MinimalismInfoEvents::CONTROLLER_INITIALISED());

        return $response;
    }
}
