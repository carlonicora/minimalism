<?php
namespace CarloNicora\Minimalism\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ModelInterface;

class AbstractModel implements ModelInterface
{
    /** @var string  */
    private string $function;

    /** @var array  */
    private array $parameters=[];

    /** @var Document  */
    protected Document $document;

    /**
     * AbstractModel constructor.
     * @param ServiceFactory $services
     */
    public function __construct(private ServiceFactory $services)
    {
        if ($this->services->getUrl() === null) {
            $this->function = 'cli';
        } else {
            $this->function = strtolower($_SERVER['REQUEST_METHOD'] ?? 'GET');
            if ($this->function === 'post' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                if ($_SERVER['HTTP_X_HTTP_METHOD'] === 'DELETE') {
                    $this->function = 'delete';
                } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] === 'PUT') {
                    $this->function = 'put';
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
     * @return int
     */
    final public function run(): int
    {
        //TODO: BUILD THE CORRECT PARAMETERS WITH SERVICES, POSITIONED PARAMETERS AND NAMED PARAMETERS
        return $this->{$this->function}(...$this->parameters);
    }
}