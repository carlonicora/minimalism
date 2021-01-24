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
     * @param bool $inSubfolder
     * @param array|null $parameters
     * @return array
     */
    private function findModel(
        ?array $models=null, 
        bool $inSubfolder=true,
        array $parameters=null,
    ): array
    {
        $searchInAdditionalModels = !$inSubfolder;
        
        if ($models === null){
            $models = $this->models;
            $searchInAdditionalModels = true;
            $inSubfolder = false;
        }

        if ($parameters === null){
            $parameters = $this->parameters;
        }
        
        $response = [];
        foreach ($parameters as $parameterKey=>$parameter){
            if ($this->doesFolderExist($parameter, $models)) {
                $additionalParameters = $this->findModel($models[$this->getProperFolderName($parameter)], true, $this->getRemainingParameters($parameters, $parameterKey));
                if ($this->modelClass !== null){
                    $response = array_merge($response, $additionalParameters);
                    return $response;
                }
            }

            if ($this->doesModelExist($parameter, $models)) {
                $this->modelClass = $models[$this->getProperModelName($parameter)];
                $response = array_merge($response, $this->getRemainingParameters($parameters, $parameterKey));
                return $response;
            }
            
            $response[] = $parameter;
        }
        
        if ($this->modelClass === null && !$inSubfolder){
            if ($searchInAdditionalModels) {
                foreach ($this->additionalModels as $serviceModels) {
                    $response = $this->findModel($serviceModels);
                    if ($this->modelClass !== null) {
                        return $response;
                    }
                }
            }

            if (array_key_exists('*', $models)){
                $this->modelClass = $models['*'];
                $response = $this->getRemainingParameters($parameters);
                return $response;
            }
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