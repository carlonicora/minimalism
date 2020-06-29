<?php
namespace CarloNicora\Minimalism\Modules\Cli;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractModel;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;

abstract class CliModel extends AbstractModel {
    /**
     * @return ResponseInterface
     */
    abstract public function run(): ResponseInterface;
}