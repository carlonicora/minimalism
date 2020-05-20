<?php
namespace CarloNicora\Minimalism\Core\Modules\Interfaces;

use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\DecrypterInterface;
use Exception;

interface ModelInterface
{
    /**
     * ModelInterface constructor.
     * @param ServicesFactory $services
     */
    public function __construct(ServicesFactory $services);

    /**
     * @param array $passedParameters
     * @param array|null $file
     * @throws Exception
     */
    public function initialise(array $passedParameters, array $file=null) : void;

    /**
     * @param string $parameterName
     * @param $parameterValue
     */
    public function setParameter(string $parameterName, $parameterValue) : void;

    /**
     * @return array
     */
    public function getParameters() : array;

    /**
     * @param Exception $e
     * @return Response
     */
    public function getResponseFromError(Exception $e) : ResponseInterface;

    /**
     *
     */
    public function preRender() : void;

    /**
     * @param int $code
     * @param string $response
     * @return mixed
     */
    public function postRender(int $code, string $response): void;

    /**
     * @return string
     */
    public function redirect(): string;

    /**
     * @return DecrypterInterface
     */
    public function decrypter() : DecrypterInterface;

    /**
     * @param string $parameterName
     */
    public function addReceivedParameters(string $parameterName) : void;
}