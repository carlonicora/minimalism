<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Interfaces\DataLoaderInterface;
use CarloNicora\Minimalism\Interfaces\EncryptedParameterInterface;
use CarloNicora\Minimalism\Interfaces\LoggerInterface;
use CarloNicora\Minimalism\Interfaces\MinimalismObjectInterface;
use CarloNicora\Minimalism\Interfaces\ParameterInterface;
use CarloNicora\Minimalism\Interfaces\PositionedParameterInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Objects\ParameterDefinition;
use CarloNicora\Minimalism\Parameters\EncryptedParameter;
use CarloNicora\Minimalism\Parameters\PositionedEncryptedParameter;
use CarloNicora\Minimalism\Parameters\PositionedParameter;
use CarloNicora\Minimalism\Services\Pools;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

class MinimalismObjectsFactory
{
    /** @var ServiceFactory|null  */
    private static ?ServiceFactory $serviceFactory=null;

    /** @var array  */
    private static array $minimalismObjectsDefinitions=[];

    /** @var bool  */
    private static bool $minimalismObjectsDefinitionsUpdated=false;

    /** @var string|null  */
    private static ?string $cacheFile=null;

    /**
     * @param ServiceFactory $serviceFactory
     */
    public static function initialise(
        ServiceFactory $serviceFactory
    ): void
    {
        self::$serviceFactory = $serviceFactory;

        self::$cacheFile = self::$serviceFactory::getPath()->getRoot()
            . DIRECTORY_SEPARATOR . 'cache'
            . DIRECTORY_SEPARATOR . 'minimalismObjectsDefinitions.cache';

        if (is_file(self::$cacheFile)) {
            $cache = file_get_contents(self::$cacheFile);

            if ($cache !== false) {
                self::$minimalismObjectsDefinitions = unserialize($cache, [true]);
            }
        }
    }

    /**
     *
     */
    public static function terminate(
    ): void
    {
        if (self::$minimalismObjectsDefinitionsUpdated) {
            file_put_contents(self::$cacheFile, serialize(self::$minimalismObjectsDefinitions));
        }
    }

    /**
     * @param string $minimalismObjectClassName
     * @param string $functionName
     * @return array
     * @throws Exception
     */
    private static function getMinimalismObjectMethodDefinitions(
        string $minimalismObjectClassName,
        string $functionName,
    ): array
    {
        if (
            !array_key_exists($minimalismObjectClassName, self::$minimalismObjectsDefinitions)
            ||
            !array_key_exists($functionName, self::$minimalismObjectsDefinitions[$minimalismObjectClassName])
        ) {
            self::$minimalismObjectsDefinitionsUpdated = true;

            $minimalismObjectDefinitions = [];

            if (($constructor = (new ReflectionClass($minimalismObjectClassName))->getConstructor()) !== null){
                $minimalismObjectDefinitions['__construct'] = self::getMinimalismMethodDefinition(
                    $constructor
                );
            } else {
                $minimalismObjectDefinitions['__construct'] = [];
            }

            foreach ((new ReflectionClass($minimalismObjectClassName))->getMethods(ReflectionMethod::IS_PUBLIC) as $method){
                $minimalismObjectDefinitions[$method->name] = self::getMinimalismMethodDefinition($method);
            }

            self::$minimalismObjectsDefinitions[$minimalismObjectClassName] = $minimalismObjectDefinitions;
        }

        return self::$minimalismObjectsDefinitions[$minimalismObjectClassName][$functionName];
    }

    /**
     * @param ReflectionMethod $method
     * @return array
     * @throws Exception
     */
    private static function getMinimalismMethodDefinition(
        ReflectionMethod $method
    ): array
    {
        $response = [];

        /** @var ReflectionParameter $methodParameter */
        foreach ($method->getParameters() ?? [] as $methodParameter) {
            /** @var ReflectionNamedType $parameter */
            $parameter = $methodParameter->getType();

            $parameterResponse = new ParameterDefinition(
                name: $methodParameter->getName(),
                allowsNull: $methodParameter->allowsNull(),
                defaultValue: $methodParameter->isDefaultValueAvailable()
                    ? $methodParameter->getDefaultValue()
                    : null
            );

            try {
                $methodParameterType = new ReflectionClass($parameter->getName());
                if ($methodParameterType->implementsInterface(ServiceInterface::class)) {
                    $parameterResponse->setType(ParameterDefinition::PARAMETER_TYPE_SERVICE);
                    $parameterResponse->setIdentifier($methodParameterType->getName());
                } elseif ($methodParameterType->implementsInterface(LoggerInterface::class)){
                    $parameterResponse->setType(ParameterDefinition::PARAMETER_TYPE_SERVICE);
                    $parameterResponse->setIdentifier($methodParameterType->getName());
                } elseif ($methodParameterType->implementsInterface(EncryptedParameterInterface::class)) {
                    $parameterResponse->setType(ParameterDefinition::PARAMETER_TYPE_ENCRYPTER_PARAMETER);
                    $parameterResponse->setIdentifier(EncryptedParameterInterface::class);
                    if ($methodParameterType->implementsInterface(PositionedParameterInterface::class)) {
                        $parameterResponse->setIsPositionedParameter(true);
                    }
                } elseif ($methodParameterType->implementsInterface(ParameterInterface::class)) {
                    $parameterResponse->setType(ParameterDefinition::PARAMETER_TYPE_PARAMETER);
                    $parameterResponse->setIdentifier(ParameterInterface::class);
                    if ($methodParameterType->implementsInterface(PositionedParameterInterface::class)) {
                        $parameterResponse->setIsPositionedParameter(true);
                    }
                } elseif ($methodParameterType->implementsInterface(DataLoaderInterface::class)) {
                    $parameterResponse->setType(ParameterDefinition::PARAMETER_TYPE_LOADER);
                    $parameterResponse->setIdentifier($methodParameterType->getName());
                } elseif ($parameter->getName() === Document::class) {
                    $parameterResponse->setType(ParameterDefinition::PARAMETER_TYPE_DOCUMENT);
                    $parameterResponse->setIdentifier(Document::class);
                }
            } catch (ReflectionException) {
                $parameterResponse->setType(ParameterDefinition::PARAMETER_TYPE_SIMPLE);
                $parameterResponse->setIdentifier($parameter->getName());
            }

            $response[] = $parameterResponse;
        }

        return $response;
    }

    /**
     * @param string $minimalismObjectClassName
     * @param array $parameters
     * @return MinimalismObjectInterface
     * @throws Exception
     */
    public static function create(
        string $minimalismObjectClassName,
        array $parameters=[],
    ): MinimalismObjectInterface
    {
        $methodParametersDefinitions = self::getMinimalismObjectMethodDefinitions(
            minimalismObjectClassName: $minimalismObjectClassName,
            functionName: '__construct'
        );

        $methodParameters = self::generateParameters(
            methodParametersDefinitions: $methodParametersDefinitions,
            parameters: $parameters
        );

        return new $minimalismObjectClassName(...$methodParameters);
    }

    /**
     * @param MinimalismObjectInterface|string $minimalismObject
     * @param string $functionName
     * @param array $parameters
     * @return mixed
     * @throws Exception
     */
    public static function call(
        MinimalismObjectInterface|string $minimalismObject,
        string $functionName,
        array $parameters=[]
    ): mixed
    {
        if (is_string($minimalismObject)){
            $minimalismObject = self::create(
                minimalismObjectClassName: $minimalismObject,
                parameters: $parameters,
            );
        }

        $methodParametersDefinitions = self::getMinimalismObjectMethodDefinitions(
            minimalismObjectClassName: $minimalismObject::class,
            functionName: $functionName
        );

        $methodParameters = self::generateParameters(
            methodParametersDefinitions: $methodParametersDefinitions,
            parameters: $parameters
        );

        return $minimalismObject->$functionName(...$methodParameters);
    }

    /**
     * @param ParameterDefinition[] $methodParametersDefinitions
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    private static function generateParameters(
        array $methodParametersDefinitions,
        array $parameters
    ): array
    {
        $response = [];

        foreach ($methodParametersDefinitions ?? [] as $methodParameter) {
            switch ($methodParameter->getType()) {
                case ParameterDefinition::PARAMETER_TYPE_SERVICE:
                    $parameterValue = self::$serviceFactory->create(
                        serviceName: $methodParameter->getIdentifier()
                    );

                    if (is_string($parameterValue)){
                        $parameterValue = self::$serviceFactory->create(
                            serviceName: $parameterValue
                        );
                    }
                    break;
                case ParameterDefinition::PARAMETER_TYPE_ENCRYPTER_PARAMETER:
                    $parameterValue = self::getEncryptedParameter(
                        parameterName: $methodParameter->getName(),
                        isPositionedParameter: $methodParameter->isPositionedParameter(),
                        parameters: $parameters,
                    );
                    break;
                case ParameterDefinition::PARAMETER_TYPE_PARAMETER:
                    $parameterValue = self::getParameter(
                        parameterName: $methodParameter->getName(),
                        isPositionedParameter: $methodParameter->isPositionedParameter(),
                        defaultParameterValue: $methodParameter->getDefaultValue(),
                        parameters: $parameters
                    );
                    break;
                case ParameterDefinition::PARAMETER_TYPE_LOADER:
                    /** @var Pools $pools */
                    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                    $parameterValue = self::$serviceFactory->create(serviceName: Pools::class)->get(
                        className: $methodParameter->getIdentifier()
                    );
                    break;
                case ParameterDefinition::PARAMETER_TYPE_DOCUMENT:
                    $parameterValue = null;
                    if (
                        array_key_exists('named', $parameters)
                        &&
                        array_key_exists($methodParameter->getName(), $parameters['named'])
                    ){
                        $value = $parameters['named'][$methodParameter->getName()];
                    } elseif (
                        array_key_exists($methodParameter->getName(), $parameters)
                    ){
                        $value = $parameters[$methodParameter->getName()];
                    } else {
                        $value = null;
                    }

                    if ($value !== null){
                        $parameterValue = new Document($value);
                    }
                    break;
                default:
                    $parameterValue = self::getSimpleParameter(
                        parameterName: $methodParameter->getName(),
                        defaultParameterValue: $methodParameter->getDefaultValue(),
                        parameters: $parameters,
                    );
                    break;
            }

            if ($parameterValue === null && !$methodParameter->allowsNull()){
                throw new RuntimeException(
                    'Required parameter ' . $methodParameter->getName() . ' missing',
                    412
                );
            }

            $response[] = $parameterValue;
        }

        return $response;
    }

    /**
     * @param string $parameterName
     * @param bool $isPositionedParameter
     * @param array $parameters
     * @return PositionedParameterInterface|ParameterInterface|null
     */
    private static function getEncryptedParameter(
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
        if (self::$serviceFactory->getEncrypter() !== null) {
            /** @var PositionedEncryptedParameter $parameterClass */
            $parameterClass->setEncrypter(self::$serviceFactory->getEncrypter());
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
    private static function getParameter(
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

    /**
     * @param string $parameterName
     * @param mixed $defaultParameterValue
     * @param array $parameters
     * @return mixed
     * @throws Exception
     */
    private static function getSimpleParameter(
        string $parameterName,
        mixed $defaultParameterValue,
        array $parameters,
    ): mixed
    {
        //TODO add casting to bool/string/whatever or throw an error if the value does not match the type
        if (
            array_key_exists('named', $parameters)
            &&
            array_key_exists($parameterName, $parameters['named'])
        ){
            return $parameters['named'][$parameterName];
        }

        if (
            array_key_exists($parameterName, $parameters)
        ){
            return $parameters[$parameterName];
        }

        return $defaultParameterValue;
    }
}