<?php
namespace CarloNicora\Minimalism\Core\JsonApi;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use CarloNicora\Minimalism\Core\Response;
use Exception;
use JsonException;

class JsonApiResponse extends Response
{
    /** @var Document|null */
    private ?Document $document = null;

    /**
     * @return Document
     * @throws Exception
     */
    public function getDocument(): Document
    {
        return $this->document ?? new Document();
    }

    /**
     * @param Document $document
     */
    public function setDocument(Document $document): void
    {
        $this->document = $document;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        if ($this->document !== null && parent::getData() === '') {
            try {
                return $this->document->export();
            } catch (JsonException $e) {
                $this->setStatus(ResponseInterface::HTTP_STATUS_500);
                return '';
            }
        }

        return parent::getData();
    }
}