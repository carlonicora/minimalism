<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Interfaces;

interface ParameterInterface
{
    public const NAME = 'name';
    public const IS_REQUIRED = 'required';
    public const IS_ENCRYPTED = 'encrypted';
    public const TYPE = 'type';
    public const VALIDATOR = 'validator';
    public const JSONAPI = 'jsonapi';
}