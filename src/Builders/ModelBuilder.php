<?php
namespace CarloNicora\Minimalism\Builders;

use RuntimeException;

class ModelBuilder
{
    /** @var string|null  */
    private ?string $modelClass=null;

    /** @var array  */
    private array $modelParameters;

    /**
     * ModelBuilder constructor.
     * @param array $parameters
     * @param array $models
     * @param array $additionalModels
     */
    public function __construct(
        private array $parameters,
        private array $models,
        private array $additionalModels,
    )
    {
        $this->modelParameters = $this->findModel();
    }

    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return $this->modelClass ?? throw new RuntimeException('Model not found', 404);
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        if ($this->modelClass === null){
            throw new RuntimeException('Model not found', 404);
        }

        return $this->modelParameters;
    }

    /**
     * @param array|null $models
     * @param array|null $parameters
     * @return array
     */
    private function findModel(
        ?array $models=null,
        array $parameters=null,
    ): array
    {
        $isFirstLevelCall = ($models === null);

        if ($isFirstLevelCall) {
            $mergedAdditionalModels = array_reduce(
                array: $this->additionalModels ?? [],
                callback: static function(array $carry, array $item)
                {
                    return array_merge_recursive($carry, $item);
                },
                initial: []
            );

            $models = array_merge_recursive($mergedAdditionalModels, $this->models);
        }

        if ($parameters === null){
            $parameters = $this->parameters;
        }

        $response = [];
        foreach ($parameters as $parameterKey=>$parameter){
            $folderExists = $this->doesFolderExist($parameter, $models);
            if ($folderExists) {
                $additionalParameters = $this->findModel(
                    models: $models[$this->getProperFolderName($parameter)],
                    parameters: $this->getRemainingParameters($parameters, $parameterKey)
                );

                if ($this->modelClass !== null){
                    return array_merge($response, $additionalParameters);
                }
            }

            if ($this->doesModelExist($parameter, $models)) {
                $modelClass = $models[$this->getProperModelName($parameter)];
                if (is_array($modelClass)){
                    $modelClass = end($modelClass);
                }
                $this->modelClass = $modelClass;
                return array_merge($response, $this->getRemainingParameters($parameters, $parameterKey));
            }

            if ($folderExists) {
                break;
            }

            $response[] = $parameter;
        }

        if ($this->modelClass === null && $isFirstLevelCall && array_key_exists('*', $models)) {
            // A model not found, but the default model exists
            $this->modelClass = $models['*'];
            return $this->getRemainingParameters($parameters);
        }

        return $response;
    }

    /**
     * @param array $parameters
     * @param int $skip
     * @return array
     */
    private function getRemainingParameters(array $parameters, int $skip=0): array
    {
        $response = [];
        foreach ($parameters as $parameterKey=>$parameter){
            if ($parameterKey>$skip){
                $response[] = $parameter;
            }
        }

        return $response;
    }

    /**
     * @param string $folderName
     * @param array $models
     * @return bool
     */
    private function doesFolderExist(string $folderName, array $models): bool
    {
        return (array_key_exists($this->getProperFolderName($folderName), $models));
    }

    /**
     * @param string $modelName
     * @param array $models
     * @return bool
     */
    private function doesModelExist(string $modelName, array $models): bool
    {
        return (array_key_exists($this->getProperModelName($modelName), $models));
    }

    /**
     * @param string $folderName
     * @return string
     */
    private function getProperFolderName(string $folderName): string
    {
        return strtolower($folderName) . '-folder';
    }

    /**
     * @param string $modelName
     * @return string
     */
    private function getProperModelName(string $modelName): string
    {
        $modelName = str_replace('-', '', $modelName);
        return strtolower($modelName);
    }
}