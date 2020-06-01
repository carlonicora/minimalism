<?php
namespace CarloNicora\Minimalism\Core\Modules\Factories;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;

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
     * @param string $controllerClassName
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

        /** @var ControllerInterface $response */
        $response = new $controllerClassName($this->services);
        $response->setSecurityInterface($this->services->getSecurityInterface());
        $response->setEncrypterInterface($this->services->getEncrypterInterface());

        return $response;
    }
}