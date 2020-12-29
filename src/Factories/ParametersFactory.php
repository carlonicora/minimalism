<?php
namespace CarloNicora\Minimalism\Factories;

use Exception;
use JsonException;
use RuntimeException;

class ParametersFactory
{
    /** @var string|null  */
    private ?string $modelName=null;

    /**
     * ParametersFactory constructor.
     * @param ServiceFactory $services
     * @param array $models
     */
    public function __construct(private ServiceFactory $services, private array $models)
    {}

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return $this->modelName;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function createParameters(): array
    {
        if ($this->services->getUrl() === null){
            return $this->getCliParameters();
        }

        return $this->getWebParameters();
    }

    /**
     * @return array
     */
    private function getCliParameters(): array
    {
        $response = [];

        $typeName = null;
        foreach ($_SERVER['argv'] ?? [] as $item) {
            if (str_starts_with($item, '-')){
                while (str_starts_with($item, '-')){
                    $item = substr($item, 1);
                }
                $typeName = $item;
            } elseif ($typeName !== null) {
                $response[$typeName] = $item;
                $typeName = null;
            } else {
                try {
                    $response = json_decode($item, true, 512, JSON_THROW_ON_ERROR);
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
        [$uriString, $namedParametersString] = explode('?', $_SERVER['REQUEST_URI'] ?? '');

        if ($uriString === '/'){
            $this->modelName = $this->models['*'];
        } else {
            $response['positioned'] = $this->getPositionedParameters($uriString);
        }

        $response['named'] = $this->getNamedParameters($namedParametersString);

        return $response;
    }

    /**
     * @param string|null $uri
     * @return array
     * @throws Exception
     */
    private function getPositionedParameters(?string $uri): array
    {
        $response = [];

        if ($uri === null || $uri === ''){
            return $response;
        }

        $uriParts = explode('/', substr($uri, 1));

        if ($uriParts === []){
            return [];
        }

        $parameterValue = current($uriParts);

        if (stripos($parameterValue, 'v') === 0 && is_numeric($parameterValue[1]) && !str_starts_with($parameterValue, '.')){
            array_shift($parameterValue);
        }

        $rollbackUriParts = $uriParts;
        if (array_key_exists($uriParts[0] . '-folder', $this->models)) {
            $response = $this->getPositionedParametersInModelFolder($this->models[$uriParts[0] . '-folder'], $uriParts);
        }

        if ($this->modelName === null) {
            $uriParts = $rollbackUriParts;
            $response = $this->getPositionedParametersInModel($this->models, $uriParts);
        }

        return $response;
    }

    /**
     * @param array $modelFolder
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    private function getPositionedParametersInModelFolder(array $modelFolder, array &$parameters): array
    {
        $rollbackParameters = $parameters;

        $response = [];
        $modelName = array_shift($parameters);
        if (!array_key_exists(strtolower($modelName . '-folder'), $modelFolder)){
            return $response;
        }

        $response[] = array_shift($parameters);

        $additionalResponses = $this->getPositionedParametersInModelFolder($modelFolder[$parameters[0] . '-folder'], $parameters);

        if ($this->modelName === null){
            $parameters = $rollbackParameters;
            $additionalResponses = $this->getPositionedParametersInModel($modelFolder, $parameters);
        }

        $response = array_merge($response, $additionalResponses);

        return $response;
    }

    /**
     * @param array $modelFolder
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    private function getPositionedParametersInModel(array $modelFolder, array &$parameters): array
    {
        $response = [];
        $modelName = array_shift($parameters);

        if (!array_key_exists(strtolower($modelName), $modelFolder)){
            if (array_key_exists('*', $this->models)){
                $this->modelName = $this->models['*'];
                array_unshift($parameters, $modelName);
            } else {
                throw new RuntimeException('Model not found', 404);
            }
        } else {
            $this->modelName = $modelFolder[$modelName];
        }

        while ($parameters !== []){
            $response[] = array_shift($parameters);
        }

        return $response;
    }

    /**
     * @param string|null $namedParametersString
     * @return array
     */
    private function getNamedParameters(?string $namedParametersString): array
    {
        $response = [];
        if ($namedParametersString === null || $namedParametersString === ''){
            return $response;
        }

        $namedParameters = explode('&', $namedParametersString);
        foreach ($namedParameters ?? [] as $namedParameter) {
            [$parameterName, $parameterValue] = explode('=', $namedParameter);
            $response[$parameterName] = $parameterValue;
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