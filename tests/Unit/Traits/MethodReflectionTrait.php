<?php
namespace CarloNicora\Minimalism\Tests\Unit\Traits;

use ReflectionClass;
use ReflectionException;

trait MethodReflectionTrait
{
    protected function setAttribute(&$object, $attributeName, $value) : void
    {
        try {
            $reflection = new ReflectionClass(get_class($object));
            $attribute = $reflection->getProperty($attributeName);
            $attribute->setAccessible(true);
            $attribute->setValue($reflection, $value);
        } catch (ReflectionException $e) {
        }
    }
}