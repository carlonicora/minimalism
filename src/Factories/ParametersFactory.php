<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Commands\FunctionParametersCommand;
use CarloNicora\Minimalism\Commands\ParametersReaderCommand;
use Exception;
use RuntimeException;

class ParametersFactory
{
    /** @var string|null  */
    private ?string $modelClass=null;

    /**
     * ParametersFactory constructor.
     * @param ServiceFactory $services
     * @param array|null $models
     * @param array|null $modelsDefinitions
     */
    public function __construct(
        private ServiceFactory $services,
        private ?array $models=null,
        private ?array $modelsDefinitions=null
    )
    {

    }

    /**
     * @return string
     */
    public function getModelClass(

    ): string
    {
        return $this->modelClass;
    }

    /**
     * @param array $modelDefinition
     * @param string $function
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    public function getModelFunctionParameters(
        array $modelDefinition,
        string $function,
        array $parameters,
    ): array
    {
        if (!array_key_exists($function, $modelDefinition)){
            throw new RuntimeException('Method not found', 404);
        }

        $functionParametersCommand = new FunctionParametersCommand(
            $this->services
        );
        
        return $functionParametersCommand->generateFunctionParameters(
            functionDefinition: $modelDefinition[$function],
            parameters: $parameters,
        );
    }

    /**
     * @return array
     * @throws Exception
     */
    public function createParameters(): array
    {
        $parametersReader = new ParametersReaderCommand(
            path: $this->services->getPath(),
            models: $this->models,
        );

        if ($this->services->getPath()->getUrl() === null){
            $response = $parametersReader->getCliParameters();
        } else {
            $response = $parametersReader->getWebParameters();
        }

        $this->modelClass = $parametersReader->getModelClass();

        return $response;
    }

}