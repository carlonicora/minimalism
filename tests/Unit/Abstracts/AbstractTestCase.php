<?php
namespace CarloNicora\Minimalism\Tests\Unit\Abstracts;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Factories\ServiceFactory;
use CarloNicora\Minimalism\Tests\Unit\Factories\MockFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

abstract class AbstractTestCase extends TestCase
{
    /** @var MockFactory  */
    protected MockFactory $mockFactory;

    /** @var ServicesFactory|null  */
    protected ?ServicesFactory $services;

    /**
     * AbstractTestCase constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->mockFactory = new MockFactory();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->services = new ServicesFactory();
        $this->services->loadService(ServiceFactory::class);
    }

    /**
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed|null
     */
    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        try {
            $reflection = new ReflectionClass(get_class($object));
            $method = $reflection->getMethod($methodName);
            $method->setAccessible(true);
            return $method->invokeArgs($object, $parameters);
        } catch (ReflectionException $e) {
            return null;
        }
    }

    /**
     * @param $object
     * @param $parameterName
     * @return mixed|null
     */
    protected function getProperty($object, $parameterName)
    {
        try {
            $reflection = new ReflectionClass(get_class($object));
            $property = $reflection->getProperty($parameterName);
            $property->setAccessible(true);
            return $property->getValue($object);
        } catch (ReflectionException $e) {
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
            $reflection = new ReflectionClass(get_class($object));
            $property = $reflection->getProperty($parameterName);
            $property->setAccessible(true);
            $property->setValue($object, $parameterValue);
        } catch (ReflectionException $e) {
        }
    }
}