<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\ParametersFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use Exception;

class AbstractModel implements ModelInterface
{
    /** @var string|null  */
    protected ?string $view=null;

    /** @var array|null  */
    protected ?array $redirectionParameters=null;

    /** @var string|null  */
    protected ?string $redirection=null;

    /** @var array  */
    private array $parameters=[];

    /** @var Document  */
    protected Document $document;

    /** @var string|null  */
    protected ?string $preRenderFunctionName=null;

    /** @var string|null  */
    protected ?string $postRenderFunctionName=null;

    /**
     * AbstractModel constructor.
     * @param ServiceFactory $services
     * @param array $modelDefinition
     * @param string|null $function
     */
    public function __construct(
        private ServiceFactory $services,
        private array $modelDefinition,
        private ?string $function=null,
    )
    {
        if ($this->function === null) {
            if ($this->services->getPath()->getUrl() === null) {
                $this->function = 'cli';
            } else {
                $this->function = strtolower($_SERVER['REQUEST_METHOD'] ?? 'GET');
                if ($this->function === 'post' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                    if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'DELETE') {
                        $this->function = 'delete';
                    } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PUT') {
                        $this->function = 'put';
                    } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PATCH') {
                        $this->function = 'patch';
                    }
                }
            }
        }

        $this->document = new Document();
    }

    /**
     * @param array $parameters
     */
    final public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return Document
     */
    final public function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * @return string|null
     */
    final public function getView(): ?string
    {
        return $this->view;
    }

    /**
     * @return string|null
     */
    final public function getRedirection(): ?string
    {
        return $this->redirection;
    }

    /**
     * @return array|null
     */
    final public function getRedirectionParameters(): ?array
    {
        return $this->redirectionParameters;
    }

    /**
     * @return string|null
     */
    final public function getRedirectionFunction(): ?string
    {
        return $this->function;
    }

    /**
     * @return callable|null
     */
    final public function getPreRenderFunction(): ?callable
    {
        if ($this->preRenderFunctionName !== null){
            return [$this, $this->preRenderFunctionName];
        }

        return null;
    }

    /**
     * @return callable|null
     */
    final public function getPostRenderFunction(): ?callable
    {
        if ($this->postRenderFunctionName !== null){
            return [$this, $this->postRenderFunctionName];
        }

        return null;
    }

    /**
     * @param string $modelClass
     * @param string|null $function
     * @param array|null $namedParameters
     * @param array|null $positionedParameters
     * @return int
     */
    final protected function redirect(
        string $modelClass,
        ?string $function=null,
        ?array $namedParameters=[],
        ?array $positionedParameters=[]
    ): int
    {
        $this->redirection = $modelClass;

        if ($function !== null){
            $this->function = $function;
        }

        $this->redirectionParameters = [
            'named' => $namedParameters,
            'positioned' => $positionedParameters
        ];

        return 302;
    }

    /**
     * @return int
     * @throws Exception
     */
    final public function run(): int
    {
        $parametersFactory = new ParametersFactory(
            services: $this->services,
        );

        $parameters = $parametersFactory->getModelFunctionParameters(
            modelDefinition: $this->modelDefinition,
            function: $this->function,
            parameters: $this->parameters
        );

        return $this->{$this->function}(...$parameters);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getParameterValue(string $name): mixed
    {
        return $this->parameters['named'][$name]??null;
    }
}