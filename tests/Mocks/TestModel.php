<?php


namespace CarloNicora\Minimalism\Tests\Mocks;


use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\DecrypterInterface;
use Exception;

class TestModel implements ModelInterface
{

    public function __construct(ServicesFactory $services)
    {
    }

    public function initialise(array $passedParameters, array $file = null): void
    {
        // TODO: Implement initialise() method.
    }

    public function setParameter(string $parameterName, $parameterValue): void
    {
        // TODO: Implement setParameter() method.
    }

    public function getParameters(): array
    {
        // TODO: Implement getParameters() method.
    }

    public function getResponseFromError(Exception $e): ResponseInterface
    {
        // TODO: Implement getResponseFromError() method.
    }

    public function preRender(): void
    {
        // TODO: Implement preRender() method.
    }

    public function postRender(ResponseInterface $response): void
    {
        // TODO: Implement preRender() method.
    }

    public function setVerb(string $verb): void
    {
        // TODO: Implement setVerb() method.
    }

    public function redirect(): string
    {
        // TODO: Implement redirect() method.
    }

    public function decrypter(): DecrypterInterface
    {
        // TODO: Implement decrypter() method.
    }

    public function addReceivedParameters(string $parameterName): void
    {
        // TODO: Implement addReceivedParameters() method.
    }

    public function setEncrypter(?EncrypterInterface $encrypter): void
    {
        // TODO: Implement setEncrypter() method.
    }
}
