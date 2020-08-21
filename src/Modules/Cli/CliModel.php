<?php
namespace CarloNicora\Minimalism\Modules\Cli;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Core\JsonApi\Traits\JsonApiModelTrait;
use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractCliModel;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;

abstract class CliModel extends AbstractCliModel
{
    use JsonApiModelTrait;

    /** @var JsonApiResponse  */
    protected JsonApiResponse $response;

    /** @var Document  */
    protected Document $document;

    /**
     * AbstractModel constructor.
     * @param ServicesFactory $services
     * @throws Exception
     */
    public function __construct(ServicesFactory $services)
    {
        parent::__construct($services);

        $this->document = new Document();
    }

    /**
     * @param array $passedParameters
     * @param array|null $file
     * @throws Exception
     */
    public function initialise(array $passedParameters, array $file = null): void
    {
        parent::initialise($passedParameters, $file);

        $this->response = new JsonApiResponse();
    }

    /**
     * @return ResponseInterface
     */
    abstract public function run(): ResponseInterface;
}