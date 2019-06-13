<?php
namespace carlonicora\minimalism\abstracts;

abstract class functions {
    /** @var  abstractConfigurations */
    protected $configurations;

    public function __construct($configurations){
        $this->configurations = $configurations;
    }
}