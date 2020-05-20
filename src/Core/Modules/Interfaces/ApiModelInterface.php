<?php
namespace CarloNicora\Minimalism\Core\Modules\Interfaces;

use CarloNicora\Minimalism\Core\Response;

interface ApiModelInterface extends ModelInterface
{
    /**
     * @param string $verb
     */
    public function setVerb(string $verb) : void;

    /**
     * @param $verb
     * @return mixed
     */
    public function requiresAuth($verb): bool;

    /**
     * @return Response
     */
    public function DELETE() : ResponseInterface;

    /**
     * @return Response
     */
    public function GET() : ResponseInterface;

    /**
     * @return Response
     */
    public function POST() : ResponseInterface;

    /**
     * @return Response
     */
    public function PUT() : ResponseInterface;
}