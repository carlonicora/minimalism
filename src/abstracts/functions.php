<?php
namespace carlonicora\minimalism\abstracts;

abstract class functions {
    /** @var  configurations */
    protected $configurations;

    public function __construct($configurations){
        $this->configurations = $configurations;
    }
}