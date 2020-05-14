<?php
namespace CarloNicora\Minimalism\Tests\Unit\Helpers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractController;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Response;
use Throwable;

class GenericController extends AbstractController
{
    public function render(): Response
    {
        return new Response();
    }

    public function initialise(string $modelName = null, array $parameterValueList = null, array $parameterValues = null): ControllerInterface
    {
        return $this;
    }
}