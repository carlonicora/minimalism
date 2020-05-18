<?php
namespace CarloNicora\Minimalism\Tests\Unit\Mocks;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractController;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Response;

class SecondaryGenericController extends AbstractController
{
    public function render(): Response
    {
        return new Response();
    }

    public function initialiseParameters(array $parameterValueList = [], array $parameterValues = []): ControllerInterface
    {
        return $this;
    }

    public function initialiseModel(string $modelName = null, string $verb = null): ControllerInterface
    {
        return $this;
    }

    public function postInitialise(): ControllerInterface
    {
        return $this;
    }
}