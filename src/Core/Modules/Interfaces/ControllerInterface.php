<?php
namespace CarloNicora\Minimalism\Core\Modules\Interfaces;

use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Logger;

interface ControllerInterface {
    /**
     * ControllerInterface constructor.
     * @param ServicesFactory $services
     */
    public function __construct(ServicesFactory $services);

    /**
     * @param string|null $modelName
     * @param array|null $parameterValueList
     * @param array|null $parameterValues
     * @return ControllerInterface
     */
    public function initialise(string $modelName=null, array $parameterValueList=null, array $parameterValues=null): ControllerInterface;

    /**
     * @return Response
     */
    public function render() : Response;
}