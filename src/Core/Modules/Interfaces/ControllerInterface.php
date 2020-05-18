<?php
namespace CarloNicora\Minimalism\Core\Modules\Interfaces;

use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;

interface ControllerInterface
{
    /**
     * ControllerInterface constructor.
     * @param ServicesFactory $services
     */
    public function __construct(ServicesFactory $services);

    /**
     * @param array $parameterValueList
     * @param array $parameterValues
     * @return ControllerInterface
     */
    public function initialiseParameters(array $parameterValueList=[], array $parameterValues=[]): ControllerInterface;

    /**
     * @param string $modelName
     * @return ControllerInterface
     */
    public function initialiseModel(string $modelName): ControllerInterface;

    /**
     * @return ControllerInterface
     */
    public function postInitialise() : ControllerInterface;

    /**
     * @return Response
     */
    public function render() : Response;

    /**
     * @param int|null $code
     * @param string|null $response
     */
    public function completeRender(int $code=null, string $response=null): void;
}