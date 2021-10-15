<?php
namespace CarloNicora\Minimalism\Services\DataValidator\Enums;

enum DataTypes: string
{
    case bool='boolean';
    case string='string';
    case int='integer';
    case float='double';
}