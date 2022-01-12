<?php

namespace CarloNicora\Minimalism\Tests\Stubs;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Objects\ModelParameters;

class ModelStub implements ModelInterface
{
    private MinimalismFactories $minimalismFactories;
    private ModelParameters $parameters;
    private ?string $function;

    public function __construct(MinimalismFactories $minimalismFactories, ?string $function = null)
    {
        $this->minimalismFactories = $minimalismFactories;
        $this->function = $function;
    }

    public function setParameters(ModelParameters $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getDocument(): Document
    {
        return new Document();
    }

    public function getView(): ?string
    {
        return '';
    }

    public function run(): HttpCode
    {
        return HttpCode::Accepted;
    }

    public function getRedirection(): ?string
    {
        return '';
    }

    public function getRedirectionFunction(): ?string
    {
        return null;
    }

    public function getRedirectionParameters(): ?ModelParameters
    {
        return null;
    }

    public function getPreRenderFunction(): ?callable
    {
        return null;
    }

    public function getPostRenderFunction(): ?callable
    {
        return null;
    }

    public function getParameterValue(string $name): mixed
    {
        return '';
    }
}