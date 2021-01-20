<?php
namespace CarloNicora\Minimalism\Factories;

use CarloNicora\Minimalism\Commands\FunctionParametersCommand;
use CarloNicora\Minimalism\Commands\ParametersReaderCommand;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use Exception;
use ReflectionClass;
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
        $class = new ReflectionClass($model);

        if (!$class->hasMethod($function)){
            throw new RuntimeException('Method not found', 404);
        }

        $functionParametersCommand = new FunctionParametersCommand(
            $this->services
        );
        return $functionParametersCommand->generateFunctionParameters(
            modelClass: get_class($model),
            functionName: $function,
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

        $this->modelName = $parametersReader->getModelName();

        return $response;
    }

}