<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Enums\HttpRequestMethod;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Objects\ModelParameters;
use Exception;

class AbstractModel implements ModelInterface
{
    /** @var string|null  */
    protected ?string $view=null;

    /** @var ModelParameters|null  */
    protected ?ModelParameters $redirectionParameters=null;

    /** @var string|null  */
    protected ?string $redirection=null;

    /** @var ModelParameters  */
    private ModelParameters $parameters;

    /** @var Document  */
    protected Document $document;

    /** @var ObjectFactory  */
    protected ObjectFactory $objectFactory;

    /**
     * AbstractModel constructor.
     * @param MinimalismFactories $minimalismFactories
     * @param HttpRequestMethod|null $function
     */
    public function __construct(
        private MinimalismFactories $minimalismFactories,
        private ?HttpRequestMethod $function=null,
    )
    {
        $this->objectFactory = $this->minimalismFactories->getObjectFactory();

        if ($this->function === null) {
            if ($this->minimalismFactories->getServiceFactory()->getPath()->getUrl() === null) {
                $this->function = HttpRequestMethod::Cli;
            } else {
                $this->function = HttpRequestMethod::tryFrom(strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'));
                if ($this->function === HttpRequestMethod::Post && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                    if ($_SERVER['HTTP_X_HTTP_METHOD'] === HttpRequestMethod::Delete) {
                        $this->function = HttpRequestMethod::Delete;
                    } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] === HttpRequestMethod::Put) {
                        $this->function = HttpRequestMethod::Put;
                    } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] === HttpRequestMethod::Patch) {
                        $this->function = HttpRequestMethod::Patch;
                    }
                }
            }
        }

        $this->document = new Document();
        $this->parameters = new ModelParameters();
    }

    /**
     * @param ModelParameters $parameters
     */
    final public function setParameters(
        ModelParameters $parameters,
    ): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return Document
     */
    final public function getDocument(
    ): Document
    {
        return $this->document;
    }

    /**
     * @return string|null
     */
    final public function getView(
    ): ?string
    {
        return $this->view;
    }

    /**
     * @return string|null
     */
    final public function getRedirection(
    ): ?string
    {
        return $this->redirection;
    }

    /**
     * @return ModelParameters|null
     */
    final public function getRedirectionParameters(
    ): ?ModelParameters
    {
        return $this->redirectionParameters;
    }

    /**
     * @return HttpRequestMethod|null
     */
    final public function getRedirectionFunction(
    ): ?HttpRequestMethod
    {
        return $this->function;
    }

    /**
     * @param string $modelClass
     * @param HttpRequestMethod|null $function
     * @param ModelParameters|null $parameters
     * @return HttpCode
     */
    final protected function redirect(
        string $modelClass,
        ?HttpRequestMethod $function=null,
        ?ModelParameters $parameters=null,
    ): HttpCode
    {
        $this->redirection = $modelClass;

        if ($function !== null){
            $this->function = $function;
        }

        $this->redirectionParameters = $parameters;

        return HttpCode::TemporaryRedirect;
    }

    /**
     * @return HttpCode
     * @throws Exception
     */
    final public function run(
    ): HttpCode
    {
        $parametersDefinitions = $this->minimalismFactories->getModelFactory()->getModelMethodParametersDefinition(
            modelName: static::class,
            functionName: $this->function,
        );

        $parametersValues = $this->minimalismFactories->getModelFactory()->generateMethodParametersValues(
            methodParametersDefinition: $parametersDefinitions,
            parameters: $this->parameters,
        );

        return $this->{$this->function}(...$parametersValues) ?? $this->function->getDefaultResponse();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getParameterValue(
        string $name,
    ): mixed
    {
        return $this->parameters->getNamedParameter($name);
    }
}