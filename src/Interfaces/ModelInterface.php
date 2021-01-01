<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\ServiceFactory;

interface ModelInterface
{
    /**
     * ModelInterface constructor.
     * @param ServiceFactory $services
     */
    public function __construct(ServiceFactory $services);

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void;

    /**
     * @return Document
     */
    public function getDocument(): Document;

    /**
     * @return string|null
     */
    public function getView(): ?string;

    /**
     * @return int
     */
    public function run(): int;



    /**
     * @return string|null
     */
    public function getRedirection(): ?string;

    /**
     * @return array|null
     */
    public function getRedirectionParameters(): ?array;
}