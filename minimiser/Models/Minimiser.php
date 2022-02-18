<?php
namespace CarloNicora\Minimiser\Models;

use CarloNicora\Minimalism\Abstracts\AbstractModel;
use CarloNicora\Minimalism\Enums\HttpCode;

class Minimiser extends AbstractModel
{
    /**
     * @return HttpCode
     */
    public function cli(
    ): HttpCode
    {
        return HttpCode::Ok;
    }
}