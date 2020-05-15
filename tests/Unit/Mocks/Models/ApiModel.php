<?php
namespace CarloNicora\Minimalism\Tests\Unit\Mocks\Models;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractApiModel;
use CarloNicora\Minimalism\Core\Response;

class ApiModel extends AbstractApiModel
{
    public function DELETE(): Response
    {
        return new Response();
    }

    public function GET(): Response
    {
        return new Response();
    }

    public function PUT(): Response
    {
        return new Response();
    }

    public function POST(): Response
    {
        return new Response();
    }

    public function preRender(): void
    {
    }
}