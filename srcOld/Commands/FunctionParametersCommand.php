<?php
namespace CarloNicora\Minimalism\Commands;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\MinimalismObjectsFactory;
use CarloNicora\Minimalism\Factories\ModelFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\DataValidator\Interfaces\DataValidatorInterface;
use CarloNicora\Minimalism\Interfaces\ParameterInterface;
use CarloNicora\Minimalism\Interfaces\PositionedParameterInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Parameters\EncryptedParameter;
use CarloNicora\Minimalism\Parameters\PositionedEncryptedParameter;
use CarloNicora\Minimalism\Parameters\PositionedParameter;
use CarloNicora\Minimalism\Services\Pools;
use Exception;
use RuntimeException;

class FunctionParametersCommand
{
    /**
     * FunctionParametersCommand constructor.
     * @param ServiceFactory $services
     */
    public function __construct(
        private ServiceFactory $services,
    ) {}

    /**
     * @param array $functionDefinition
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    public function generateFunctionParameters(
        array $functionDefinition,
        array $parameters,
    ): array
    {
        $response = [];

        foreach ($functionDefinition ?? [] as $parameterDefinition){
            switch ($parameterDefinition['type']) {
                case ModelFactory::PARAMETER_TYPE_SERVICE:
                    $parameterValue = $this->getServiceInterfaceParameter($parameterDefinition['identifier']);
                    if (is_string($parameterValue)){
                        $parameterValue = $this->getServiceInterfaceParameter($parameterValue);
                    }
                    break;
                case ModelFactory::PARAMETER_TYPE_ENCRYPTER_PARAMETER:
                    $parameterValue = $this->getEncryptedParameter(
                        parameterName: $parameterDefinition['name'],
                        isPositionedParameter: $parameterDefinition['isPositionedParameter'],
                        parameters: $parameters,
                    );
                    break;
                case ModelFactory::PARAMETER_TYPE_PARAMETER:
                    $parameterValue = $this->getParameter(
                        parameterName: $parameterDefinition['name'],
                        isPositionedParameter: $parameterDefinition['isPositionedParameter'],
                        defaultParameterValue: $parameterDefinition['defaultValue'],
                        parameters: $parameters
                    );
                    break;
                case ModelFactory::PARAMETER_TYPE_LOADER:
                    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                    $parameterValue = $this->getServiceInterfaceParameter(Pools::class)->get($parameterDefinition['identifier']);
                    break;
                case ModelFactory::PARAMETER_TYPE_DATA_VALIDATOR:
                    if (array_key_exists($parameterDefinition['name'], $parameters['named'])
                        && is_array($parameters['named'][$parameterDefinition['name']])
                    ){
                        /** @var DataValidatorInterface $dataValidator */
                        $dataValidator = MinimalismObjectsFactory::create($parameterDefinition['identifier']);
                        $dataValidator->setDocument($parameters['named'][$parameterDefinition['name']]);

                        if ($dataValidator->validate()) {
                            $parameterValue = $dataValidator;
                        } else {
                            throw new RuntimeException(
                                message: $dataValidator->getValidationError()?->getValidatorType()->name
                                    . ' '
                                    . $dataValidator->getValidationError()?->getError()->name
                                    . ' '
                                    . $dataValidator->getValidationError()?->getDescription(),
                                code: 412,
                            );
                        }
                    } else {
                        $parameterValue = null;
                    }
                    break;
                case ModelFactory::PARAMETER_TYPE_DOCUMENT:
                    if (array_key_exists($parameterDefinition['name'], $parameters['named'])
                        && is_array($parameters['named'][$parameterDefinition['name']])
                    ){
                        $parameterValue = $this->getDocumentParameter(
                            parameterName: $parameterDefinition['name'],
                            parameters: $parameters,
                        );
                    } else {
                        $parameterValue = null;
                    }
                    break;
                default:
                    $parameterValue = $this->getSimpleParameter(
                        parameterName: $parameterDefinition['name'],
                        defaultParameterValue: $parameterDefinition['defaultValue']??null,
                        parameters: $parameters,
                    );
                    break;
            }

            if ($parameterValue === null && $parameterDefinition['allowsNull'] === false){
                throw new RuntimeException(
                    'Required parameter ' . $parameterDefinition['name'] . ' missing',
                    412
                );
            }

            $response[] = $parameterValue;
        }

        return $response;
    }

    /**
     * @param string $parameterName
     * @param mixed $defaultParameterValue
     * @param array $parameters
     * @return mixed
     * @throws Exception
     */
    private function getSimpleParameter(
        string $parameterName,
        mixed $defaultParameterValue,
        array $parameters,
    ): mixed
    {
        //TODO add casting to bool/string/whatever or throw an error if the value does not match the type
        if (!array_key_exists($parameterName, $parameters['named'])){
            return $defaultParameterValue;
        }

        return $parameters['named'][$parameterName];
    }

    /**
     * @param string $parameterName
     * @param array $parameters
     * @return Document
     * @throws Exception
     */
    private function getDocumentParameter(
        string $parameterName,
        array $parameters,
    ): Document
    {
        return new Document(
            $parameters['named'][$parameterName]
        );
    }

    /**
     * @param string $serviceName
     * @return ServiceInterface
     * @throws Exception
     */
    private function getServiceInterfaceParameter(
        string $serviceName
    ): ServiceInterface
    {
        return $this->services->create($serviceName);
    }

    /**
     * @param string $parameterName
     * @param bool $isPositionedParameter
     * @param array $parameters
     * @return PositionedParameterInterface|ParameterInterface|null
     */
    private function getEncryptedParameter(
        string $parameterName,
        bool $isPositionedParameter,
        array &$parameters,
    ): PositionedParameterInterface|ParameterInterface|null
    {
        $newParameter = null;

        if ($isPositionedParameter) {
            $newParameterClass = PositionedEncryptedParameter::class;
            if (array_key_exists('positioned', $parameters) && array_key_exists(0, $parameters['positioned'])) {
                $newParameter = array_shift($parameters['positioned']);
            }
        } else {
            $newParameterClass = EncryptedParameter::class;
            if (array_key_exists($parameterName, $parameters['named'])){
                $newParameter = $parameters['named'][$parameterName];
            }
        }

        if ($newParameter === null){
            return null;
        }

        $parameterClass = new $newParameterClass($newParameter);
        if ($this->services->getEncrypter() !== null) {
            /** @var PositionedEncryptedParameter $parameterClass */
            $parameterClass->setEncrypter($this->services->getEncrypter());
        } else {
            throw new RuntimeException('No encrypter has been specified', 500);
        }

        return $parameterClass;
    }

    /**
     * @param string $parameterName
     * @param bool $isPositionedParameter
     * @param mixed $defaultParameterValue
     * @param array $parameters
     * @return ParameterInterface|null
     */
    private function getParameter(
        string $parameterName,
        bool $isPositionedParameter,
        mixed $defaultParameterValue,
        array &$parameters,
    ): ParameterInterface|null
    {
        $newParameter = null;

        if ($isPositionedParameter) {
            $newParameterClass = PositionedParameter::class;
            if (array_key_exists('positioned', $parameters) && array_key_exists(0, $parameters['positioned'])) {
                $newParameter = array_shift($parameters['positioned']);
            }
        } else {
            $newParameterClass = $parameterName;
            if (array_key_exists('named', $parameters) && array_key_exists($parameterName, $parameters['named'])){
                $newParameter = $parameters['named'][$parameterName];
            }
        }

        if ($newParameter === null){
            return $defaultParameterValue;
        }

        return new $newParameterClass($newParameter);
    }
}