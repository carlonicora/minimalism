<?php
namespace CarloNicora\Minimalism\Modules\Web;

use CarloNicora\JsonApi\Document;
use CarloNicora\JsonApi\Objects\Error;
use CarloNicora\Minimalism\Core\JsonApi\JsonApiResponse;
use CarloNicora\Minimalism\Core\JsonApi\Traits\JsonApiModelTrait;
use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractWebModel;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use Exception;
use RuntimeException;
use Twig\Extension\ExtensionInterface;

abstract class WebModel extends AbstractWebModel {
    use JsonApiModelTrait;

    /** @var Document  */
    protected Document $document;

    /** @var array  */
    private array $twigExtensions = [];

    /** @var Error|null  */
    private ?Error $error=null;

    /**
     * AbstractModel constructor.
     * @param ServicesFactory $services
     * @throws Exception
     */
    public function __construct(ServicesFactory $services)
    {
        parent::__construct($services);

        $this->document = new document();
        try {
            $this->document->meta->add('url', $this->services->paths()->getUrl());
        } catch (Exception $e) {}
    }

    /**
     * @param ExtensionInterface $extension
     */
    public function addTwigExtension(ExtensionInterface $extension): void {
        $this->twigExtensions[] = $extension;
    }

    /**
     * @return array
     */
    public function getTwigExtensions(): array {
        return $this->twigExtensions;
    }

    /**
     * @return responseInterface
     */
    public function generateData() : ResponseInterface{
        return new JsonApiResponse();
    }

    /**
     * @throws Exception
     */
    public function preRender() : void {
        if ($this->error !== null) {
            $errorArray = $this->error->prepare();
            throw new RuntimeException($errorArray['detail'], $errorArray['status']);
        }
    }

    /**
     * @param $parameter
     * @return Document
     * @throws Exception
     */
    public function validateJsonapiParameter($parameter): Document{
        return new Document($parameter);
    }
}