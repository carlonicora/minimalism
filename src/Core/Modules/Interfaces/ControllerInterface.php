<?php
namespace CarloNicora\Minimalism\Core\Modules\Interfaces;

use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\SecurityInterface;

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
     * @param string $verb
     * @return ControllerInterface
     */
    public function initialiseModel(string $modelName, string $verb='GET'): ControllerInterface;

    /**
     * @return ControllerInterface
     */
    public function postInitialise() : ControllerInterface;

    /**
     * @return Response
     */
    public function render() : ResponseInterface;

    /**
     * @param int|null $code
     * @param string|null $response
     */
    public function completeRender(int $code=null, string $response=null): void;

    /**
     * @param SecurityInterface|null $security
     */
    public function setSecurityInterface(?SecurityInterface $security) : void;

    /**
     * @param EncrypterInterface|null $encrypter
     */
    public function setEncrypterInterface(?EncrypterInterface $encrypter) : void;
}