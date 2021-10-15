<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Enums;

enum ValidationErrors
{
    case typeMismatch;
    case idMissing;
    case attributeMissing;
    case numberOfResourcesMismatch;
}