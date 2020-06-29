<?php


namespace CarloNicora\Minimalism\Tests\Mocks;


use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\SecurityInterface;

class TestController implements ControllerInterface
{

    public function __construct(ServicesFactory $services)
    {
    }

    public function initialiseParameters(
        array $parameterValueList = [],
        array $parameterValues = []
    ): ControllerInterface {
        return $this;
    }

    public function initialiseModel($modelName = null, string $verb = 'GET'): ControllerInterface
    {
        return $this;
    }

    public function postInitialise(): ControllerInterface
    {
        return $this;
    }

    public function render(): ResponseInterface
    {
        // TODO: Implement render() method.
    }

    public function completeRender(int $code = null, string $response = null): void
    {
        // TODO: Implement completeRender() method.
    }

    public function setSecurityInterface(?SecurityInterface $security): void
    {
        // TODO: Implement setSecurityInterface() method.
    }

    public function setEncrypterInterface(?EncrypterInterface $encrypter): void
    {
        // TODO: Implement setEncrypterInterface() method.
    }
}
