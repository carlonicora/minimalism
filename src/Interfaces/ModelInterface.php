<?php
namespace CarloNicora\Minimalism\Interfaces;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Objects\ModelParameters;

interface ModelInterface
{
    /**
     * ModelInterface constructor.
     * @param MinimalismFactories $minimalismFactories
     * @param string|null $function
     */
    public function __construct(
        MinimalismFactories $minimalismFactories,
        ?string $function=null
    );

    /**
     * @param ModelParameters $parameters
     */
    public function setParameters(ModelParameters $parameters): void;

    /**
     * @return Document
     */
    public function getDocument(): Document;

    /**
     * @return string|null
     */
    public function getView(): ?string;

    /**
     * @return HttpCode
     */
    public function run(): HttpCode;

    /**
     * @return string|null
     */
    public function getRedirection(): ?string;

    /**
     * @return string|null
     */
    public function getRedirectionFunction(): ?string;

    /**
     * @return ModelParameters|null
     */
    public function getRedirectionParameters(): ?ModelParameters;

    /**
     * @param string $name
     * @return mixed
     */
    public function getParameterValue(string $name): mixed;
}