<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Builders\ModelBuilder;
use CarloNicora\Minimalism\Interfaces\EncryptedParameterInterface;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Interfaces\ParameterInterface;
use CarloNicora\Minimalism\Interfaces\PositionedParameterInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Parameters\EncryptedParameter;
use CarloNicora\Minimalism\Parameters\PositionedEncryptedParameter;
use CarloNicora\Minimalism\Parameters\PositionedParameter;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

class ParametersFactory
{
    /** @var string|null  */
    private ?string $modelName=null;

    /**
     * ParametersFactory constructor.
     * @param ServiceFactory $services
     * @param array|null $models
     */
    public function __construct(
        private ServiceFactory $services,
        private ?array $models=null,
    )
    {}

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return $this->modelName;
    }

    /**
     * @param ModelInterface $model
     * @param string $function
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    public function getModelFunctionParameters(
        ModelInterface $model,
        string $function,
        array $parameters,
    ): array
    {
        $response = [];

        $class = new ReflectionClass($model);

        if (!$class->hasMethod($function)){
            throw new RuntimeException('Method not found', 404);
        }

        $method = new ReflectionMethod(
            get_class($model),
            $function
        );
        $methodParameters = $method->getParameters();

        foreach ($methodParameters ?? [] as $methodParameter) {
            $newParameter = null;
            $newParameterClass = null;

            /** @var ReflectionNamedType $parameter */
            $parameter = $methodParameter->getType();
            try {
                $methodParameterType = new ReflectionClass($parameter->getName());
                if ($methodParameterType->implementsInterface(ServiceInterface::class)) {
                    $classResponse = $this->services->create($parameter->getName());
                    if ($classResponse === null && !$parameter->allowsNull()){
                        throw new RuntimeException($parameter->getName() . ' missing', 500);
                    }
                    $response[] = $classResponse;
                } elseif ($methodParameterType->implementsInterface(EncryptedParameterInterface::class)) {
                    if ($methodParameterType->implementsInterface(PositionedParameterInterface::class)) {
                        $newParameterClass = PositionedEncryptedParameter::class;
                        if (array_key_exists('positioned', $parameters) && array_key_exists(0, $parameters['positioned'])) {
                            $newParameter = array_shift($parameters['positioned']);
                        }
                    } else {
                        $newParameterClass = EncryptedParameter::class;
                        /** @noinspection NotOptimalIfConditionsInspection */
                        if (array_key_exists($methodParameter->getName(), $parameters['named'])){
                            $newParameter = $parameters['named'][$methodParameter->getName()];
                        }
                    }

                    if ($newParameter !== null) {
                        $parameterClass = new $newParameterClass($newParameter);
                        if ($this->services->getEncrypter() !== null){
                            /** @var PositionedEncryptedParameter $parameterClass */
                            $parameterClass->setEncrypter($this->services->getEncrypter());
                        } else {
                            throw new RuntimeException('No encrypter has been specified', 500);
                        }

                        $response[] = $parameterClass;
                    } else {
                        $response[] = null;
                    }

                } elseif ($methodParameterType->implementsInterface(ParameterInterface::class)){
                    if ($methodParameterType->implementsInterface(PositionedParameterInterface::class)) {
                        $newParameterClass = PositionedParameter::class;
                        if (array_key_exists('positioned', $parameters) && array_key_exists(0, $parameters['positioned'])) {
                            $newParameter = array_shift($parameters['positioned']);
                        }
                    } else {
                        $newParameterClass = $methodParameterType->getName();
                        if (array_key_exists('named', $parameters) && array_key_exists($parameter->getName(), $parameters['named'])){
                            $newParameter = $parameters['named'][$parameter->getName()];
                        }
                    }

                    if ($newParameter === null && !$parameter->allowsNull()){
                        throw new RuntimeException('Required parameter missing: ' . $methodParameter->getName(), 412);
                    }

                    if ($newParameter !== null) {
                        $parameterClass = new $newParameterClass($newParameter);
                        $response[] = $parameterClass;
                    } else {
                        $response[] = $methodParameter->isDefaultValueAvailable() ? $methodParameter->getDefaultValue() : null;
                    }
                } elseif (
                    $parameter->getName() === Document::class
                    && array_key_exists($methodParameter->getName(), $parameters['named'])
                    && is_array($parameters['named'][$methodParameter->getName()])
                ) {
                    $response[] = new Document(
                        $parameters['named'][$methodParameter->getName()]
                    );
                }
            } catch (ReflectionException) {
                if (!array_key_exists($methodParameter->getName(), $parameters['named']) && !$parameter->allowsNull()){
                    throw new RuntimeException('Required parameter missing: ' . $methodParameter->getName(), 412);
                }

                if (array_key_exists('named', $parameters) && array_key_exists($methodParameter->getName(), $parameters['named'])){
                    $response[] = $parameters['named'][$methodParameter->getName()];
                } else {
                    $response[] = $methodParameter->isDefaultValueAvailable() ? $methodParameter->getDefaultValue() : null;
                }
            }
        }

        return $response;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function createParameters(): array
    {
        if ($this->services->getPath()->getUrl() === null){
            return $this->getCliParameters();
        }

        return $this->getWebParameters();
    }

    /**
     * @return array
     */
    #[ArrayShape(['named' => "array"])]
    private function getCliParameters(): array
    {
        $response = [
            'named' => []
        ];

        $typeName = null;
        foreach ($_SERVER['argv'] ?? [] as $item) {
            if (str_starts_with($item, '-')){
                while (str_starts_with($item, '-')){
                    $item = substr($item, 1);
                }
                $typeName = $item;
            } elseif ($typeName !== null) {
                $response['named'][$typeName] = $item;
                $typeName = null;
            } else {
                try {
                    $response['named']['payload'] = json_decode($item, true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException) {
                }
            }
        }

        return $response;
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getWebParameters(): array
    {
        $response = [];
        [$uri, $namedParametersString] = array_pad(
            explode('?', $_SERVER['REQUEST_URI'] ?? ''),
            2,
            ''
        );

        $this->services->getPath()->sanitiseUriVersion($uri);

        if ($uri === '/'){
            $this->modelName = $this->models['*'];
        } else {
            $uriParts = explode('/', substr($uri, 1));
            $modelBuilder = new ModelBuilder($uriParts, $this->models, $this->services->getPath()->getServicesModels());

            $this->modelName = $modelBuilder->getModel();
            $response['positioned'] = $modelBuilder->getParameters();

            unset($modelBuilder);
        }

        $response['named'] = $this->getNamedParameters($namedParametersString);

        return $response;
    }

    /**
     * @param string|null $namedParametersString
     * @return array
     */
    private function getNamedParameters(?string $namedParametersString): array
    {
        $response = [];
        if ($namedParametersString !== null && $namedParametersString !== '') {
            $namedParameters = explode('&', $namedParametersString);
            foreach ($namedParameters ?? [] as $namedParameter) {
                [$parameterName, $parameterValue] = explode('=', $namedParameter);
                $response[$parameterName] = $parameterValue;
            }
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET'){
            foreach ($_GET as $parameter => $value) {
                $response[$parameter] = $value;
            }
        } else {
            if (!empty($phpInput=file_get_contents('php://input'))) {
                try {
                    $response['payload'] = json_decode($phpInput, true, 512, JSON_THROW_ON_ERROR);
                } catch (Exception) {
                    try {
                        $additionalResponse = [];
                        parse_str($phpInput, $additionalResponse);

                        if ($additionalResponse !== []) {
                            $response = array_merge($response, $additionalResponse);
                        }
                    } catch (Exception) {
                    }
                }
            }

            $response['files'] = $this->reArrayFiles($_FILES);

            foreach ($_POST as $parameter => $value) {
                $response[$parameter] = $value;
            }
        }

        return $response;
    }

    /**
     * @param array $files
     * @return array
     */
    private function reArrayFiles(array $files): array
    {
        $result = [];
        if (empty($files)) {
            return $result;
        }

        foreach ($files as $key => $file) {
            if (is_string($file['name'])) {
                $result[$key] = $file;
            } elseif (is_array($file['name'])) {
                $result[$key] = [];
                foreach ($file as $lastKey => $value1) {
                    $result[$key] = array_replace_recursive($result[$key], $this->recursive($lastKey, $file[$lastKey]));
                }
            }

        }

        return $result;
    }

    /**
     * @param $lastKey
     * @param $input
     * @return array
     */
    private function recursive($lastKey, $input): array
    {
        $result = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->recursive($lastKey, $value);
            } else {
                $result[$key][$lastKey] = $value;
            }
        }

        return $result;
    }
}