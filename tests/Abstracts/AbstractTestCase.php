<?php
namespace CarloNicora\Minimalism\Tests\Abstracts;

use CarloNicora\Minimalism\Objects\ParameterDefinition;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

abstract class AbstractTestCase extends TestCase
{


    /**
     * @param $object
     * @param $parameterName
     * @return mixed
     */
    protected function getProperty($object, $parameterName): mixed
    {
        try {
            $property = (new ReflectionClass(get_class($object)))->getProperty($parameterName);
            $property->setAccessible(true);
            return $property->getValue($object);
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * @param $object
     * @param $parameterName
     * @param $parameterValue
     */
    protected function setProperty($object, $parameterName, $parameterValue): void
    {
        try {
            $property = (new ReflectionClass(get_class($object)))->getProperty($parameterName);
            $property->setAccessible(true);
            $property->setValue($object, $parameterValue);
        } catch (ReflectionException) {
        }
    }
}