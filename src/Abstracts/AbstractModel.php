<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Enums\HttpCache;
use CarloNicora\Minimalism\Enums\HttpCode;
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

    /** @var HttpCache  */
    protected HttpCache $httpCache=HttpCache::NoCache;

    /** @var int  */
    protected int $httpCacheExpiration=3600;

    /**
     * AbstractModel constructor.
     * @param MinimalismFactories $minimalismFactories
     * @param string|null $function
     */
    public function __construct(
        private readonly MinimalismFactories $minimalismFactories,
        protected ?string                      $function=null,
    )
    {
        $this->objectFactory = $this->minimalismFactories->getObjectFactory();

        if ($this->function === null) {
            if ($this->minimalismFactories->getServiceFactory()->getPath()?->getUrl() === null) {
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
     * @return string|null
     */
    final public function getRedirectionFunction(
    ): ?string
    {
        return $this->function;
    }

    /**
     * @param string $modelClass
     * @param string|null $function
     * @param ModelParameters|null $parameters
     * @return HttpCode
     */
    final protected function redirect(
        string $modelClass,
        ?string $function=null,
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
        if ($this->redirection !== null){
            return HttpCode::TemporaryRedirect;
        }

        $parametersDefinitions = $this->minimalismFactories->getModelFactory()->getModelMethodParametersDefinition(
            modelName: static::class,
            functionName: $this->function,
        );

        $parametersValues = $this->minimalismFactories->getModelFactory()->generateMethodParametersValues(
            methodParametersDefinition: $parametersDefinitions,
            parameters: $this->parameters,
        );

        $response = $this->{$this->function}(...$parametersValues);

        if (strtolower($this->function) === 'get'){
            $this->httpCache->write($this->httpCacheExpiration);
        }

        return $response;
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