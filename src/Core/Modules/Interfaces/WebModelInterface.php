<?php
namespace CarloNicora\Minimalism\Core\Modules\Interfaces;

use CarloNicora\Minimalism\Core\Response;

interface WebModelInterface
{
    /**
     * @return string
     */
    public function getViewName(): string;

    /**
     * @return Response
     */
    public function generateData() : Response;
}