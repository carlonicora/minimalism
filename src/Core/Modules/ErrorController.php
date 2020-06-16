<?php
namespace CarloNicora\Minimalism\Core\Modules;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Interfaces\SecurityInterface;
use Exception;

class ErrorController implements ControllerInterface
{
    /** @var ServicesFactory  */
    private ServicesFactory $services;

    /** @var Exception  */
    private Exception $exception;

    /**
     * ErrorController constructor.
     * @param ServicesFactory $services
     */
    public function __construct(ServicesFactory $services)
    {
        $this->services = $services;
    }

    /**
     * @param array|null $parameterValueList
     * @param array|null $parameterValues
     * @return ControllerInterface
     */
    public function initialiseParameters(array $parameterValueList = null, array $parameterValues = null): ControllerInterface
    {
        return $this;
    }

    /**
     * @param ModelInterface|string|null $modelName
     * @param string $verb
     * @return $this|ControllerInterface
     */
    public function initialiseModel($modelName = null, string $verb = 'GET'): ControllerInterface
    {
        return $this;
    }

    /**
     * @return $this|ControllerInterface
     */
    public function postInitialise(): ControllerInterface
    {
        return $this;
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function render(): Response
    {
        $response = new Response();

        $response->setStatus($this->exception->getCode());
        $response->setData($this->exception->getMessage());

        $this->services->logger()->error()->log(MinimalismErrorEvents::GENERIC_ERROR($this->exception));


        return $response;
    }

    /**
     * @param Exception $exception
     */
    public function setException(Exception $exception) : void
    {
        $this->exception = $exception;
    }

    /**
     * @param int|null $code
     * @param string|null $response
     */
    public function completeRender(int $code = null, string $response = null): void
    {
    }

    /**
     * @param EncrypterInterface|null $encrypter
     */
    public function setEncrypterInterface(?EncrypterInterface $encrypter): void
    {
    }

    /**
     * @param SecurityInterface|null $security
     */
    public function setSecurityInterface(?SecurityInterface $security): void
    {
    }
}
