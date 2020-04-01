<?php
namespace carlonicora\minimalism\core\models\abstracts;

abstract class abstractCliModel extends abstractModel {
    /**
     * @return bool
     */
    abstract public function run(): bool;
}