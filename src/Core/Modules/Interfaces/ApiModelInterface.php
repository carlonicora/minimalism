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
    public function DELETE() : Response;

    /**
     * @return Response
     */
    public function GET() : Response;

    /**
     * @return Response
     */
    public function POST() : Response;

    /**
     * @return Response
     */
    public function PUT() : Response;
}