<?php
namespace carlonicora\minimalism\models\abstracts;

abstract class abstractCliModel extends abstractModel {
    /**
     * @return bool
     */
    abstract public function run(): bool;
}