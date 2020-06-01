<?php
namespace CarloNicora\Minimalism\Modules\Api;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractApiModel;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Core\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Core\JsonApi\Traits\JsonApiModelTrait;
use Exception;

class ApiModel extends AbstractApiModel
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
     * @param array $includedResourceTypes
     */
    public function setIncludedResourceTypes(array $includedResourceTypes) : void
    {
        $this->document->setIncludedResourceTypes($includedResourceTypes);
    }

    /**
     * @param array $requiredFields
     */
    public function setRequiredFields(array $requiredFields) : void
    {
        $this->document->setRequiredFields($requiredFields);
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
     * @throws ServiceNotFoundException
     * @throws ConfigurationException|Exception
     */
    public function DELETE(): ResponseInterface {
        return $this->generateResponseFromError(new Exception('Not implemented', (int)Response::HTTP_STATUS_405));
    }

    /**
     * @return ResponseInterface
     * @throws ServiceNotFoundException
     * @throws ConfigurationException|Exception
     */
    public function GET(): ResponseInterface {
        return $this->generateResponseFromError(new Exception('Not implemented', (int)Response::HTTP_STATUS_405));
    }

    /**
     * @return ResponseInterface
     * @throws ServiceNotFoundException
     * @throws ConfigurationException|Exception
     */
    public function POST(): ResponseInterface {
        return $this->generateResponseFromError(new Exception('Not implemented', (int)Response::HTTP_STATUS_405));
    }

    /**
     * @return ResponseInterface
     * @throws ServiceNotFoundException
     * @throws ConfigurationException|Exception
     */
    public function PUT(): ResponseInterface {
        return $this->generateResponseFromError(new Exception('Not implemented', (int)Response::HTTP_STATUS_405));
    }

    /**
     *
     */
    public function preRender(): void
    {
    }
}