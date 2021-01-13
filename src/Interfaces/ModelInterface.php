<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Factories\ServiceFactory;

interface ModelInterface
{
    /**
     * ModelInterface constructor.
     * @param ServiceFactory $services
     * @param string|null $function
     */
    public function __construct(ServiceFactory $services, ?string $function=null);

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
     * @return string|null
     */
    public function getRedirectionFunction(): ?string;

    /**
     * @return array|null
     */
    public function getRedirectionParameters(): ?array;
}