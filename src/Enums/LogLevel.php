<?php
namespace CarloNicora\Minimalism\Enums;

enum LogLevel: int
{
    case Debug=100;
    case Info=200;
    case Notice=250;
    case Warning=300;
    case Error=400;
    case Critical=500;
    Case Alert=550;
    Case Emergency=600;
}