<?php
namespace CarloNicora\Minimalism\Minimise\Models;

use CarloNicora\Minimalism\Abstracts\AbstractModel;

class Minimise extends AbstractModel
{
    public function cli(): int
    {
        echo "this is a test";
        return 200;
    }
}