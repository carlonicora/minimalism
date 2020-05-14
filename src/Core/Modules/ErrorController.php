<?php
namespace CarloNicora\Minimalism\Core\Modules;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ControllerInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Traits\LoggerTrait;
use Exception;

class ErrorController implements ControllerInterface
{
    use LoggerTrait;

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
     * @param string|null $modelName
     * @param array|null $parameterValueList
     * @param array|null $parameterValues
     * @return ControllerInterface
     */
    public function initialise(string $modelName = null, array $parameterValueList = null, array $parameterValues = null): ControllerInterface {
        return $this;
    }

    /**
     * @return Response
     */
    public function render(): Response
    {
        $response = new Response();

        $response->httpStatus = $this->exception->getCode();
        $response->data = $this->exception->getMessage();

        if ($this->services !== null) {
            $this->loggerInitialise($this->services);
            $this->loggerWriteError($this->exception->getCode(),
                $this->exception->getMessage(),
                'minimalism',
                $this->exception);
        }

        return $response;
    }

    /**
     * @param Exception $exception
     */
    public function setException(Exception $exception) : void
    {
        $this->exception = $exception;
    }
}