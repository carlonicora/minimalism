<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Enums;

enum ValidatorTypes
{
    case document;
    case resource;
    case attribute;
    case meta;
    case link;
}