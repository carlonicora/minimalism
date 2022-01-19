<?php

namespace CarloNicora\Minimalism\Tests\Unit\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Abstracts\AbstractFactory;
use CarloNicora\Minimalism\Enums\ParameterType;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Objects\ParameterDefinition;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;
use CarloNicora\Minimalism\Tests\Stubs\ModelStub;
use CarloNicora\Minimalism\Tests\Stubs\ObjectStub;
use CarloNicora\Minimalism\Tests\Stubs\ParameterStub;
use CarloNicora\Minimalism\Tests\Stubs\PositionedParameterStub;
use CarloNicora\Minimalism\Tests\Stubs\ServiceStub;
use CarloNicora\Minimalism\Tests\Stubs\SimpleObjectStub;
use RuntimeException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Class AbstractFactoryTest
 * @package CarloNicora\Minimalism\Tests\Unit\Abstracts\
 * @coversDefaultClass  \CarloNicora\Minimalism\Abstracts\AbstractFactory
 */
class AbstractFactoryTest extends AbstractTestCase
{
    private MinimalismFactories $minimalismFactories;
    private AbstractFactory $factory;

    /**
     * @return void
     */
    public function setUp(
    ): void
    {
        parent::setUp();

        $this->minimalismFactories = $this->createMock(MinimalismFactories::class);
        $this->factory = new class ($this->minimalismFactories) extends AbstractFactory {};
    }

    /**
     * @covers ::getMethodParametersDefinition
     * @return void
     */
    public function testItShouldGetMethodParametersDefinitionService(
    ): void
    {
        $reflectionMethod = $this->createMock(ReflectionMethod::class);
        $reflectionParameter = $this->createMock(ReflectionParameter::class);
        $parameter = $this->createMock(ReflectionNamedType::class);
        $className = ServiceStub::class;
        $methodParameterName = 'setObjectFactory';
        $defaultValue = 'default-value';
        $reflectionMethod->expects($this->once())
            ->method('getParameters')
            ->willReturn([$reflectionParameter]);
        $reflectionParameter->expects($this->once())
            ->method('getName')
            ->willReturn($methodParameterName);
        $reflectionParameter->expects($this->once())
            ->method('allowsNull')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('isDefaultValueAvailable')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('getDefaultValue')
            ->willReturn($defaultValue);
        $reflectionParameter->expects($this->once())
            ->method('getType')
            ->willReturn($parameter);
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $result = $this->invokeMethod(
            object: $this->factory,
            methodName: 'getMethodParametersDefinition',
            arguments: [$reflectionMethod]
        );
        /** @var ParameterDefinition $parameterDefinition */
        $parameterDefinition = $result[0];

        $this->assertIsArray($result);
        $this->assertInstanceOf(
            expected: ParameterDefinition::class,
            actual: $parameterDefinition
        );
        $this->assertTrue($parameterDefinition->allowsNull());
        $this->assertEquals(
            expected: $methodParameterName,
            actual: $parameterDefinition->getName()
        );
        $this->assertEquals(
            expected: $className,
            actual: $parameterDefinition->getIdentifier()
        );
        $this->assertEquals(
            expected: ParameterType::Service,
            actual: $parameterDefinition->getType()
        );
        $this->assertEquals(
            expected: $defaultValue,
            actual: $parameterDefinition->getDefaultValue()
        );
    }

    /**
     * @covers ::getMethodParametersDefinition
     * @return void
     */
    public function testItShouldGetMethodParametersDefinitionMinimalismFactories(
    ): void
    {
        $reflectionMethod = $this->createMock(ReflectionMethod::class);
        $reflectionParameter = $this->createMock(ReflectionParameter::class);
        $parameter = $this->createMock(ReflectionNamedType::class);
        $className = MinimalismFactories::class;
        $methodParameterName = 'setObjectFactory';
        $defaultValue = 'default-value';
        $reflectionMethod->expects($this->once())
            ->method('getParameters')
            ->willReturn([$reflectionParameter]);
        $reflectionParameter->expects($this->once())
            ->method('getName')
            ->willReturn($methodParameterName);
        $reflectionParameter->expects($this->once())
            ->method('allowsNull')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('isDefaultValueAvailable')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('getDefaultValue')
            ->willReturn($defaultValue);
        $reflectionParameter->expects($this->once())
            ->method('getType')
            ->willReturn($parameter);
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $result = $this->invokeMethod(
            object: $this->factory,
            methodName: 'getMethodParametersDefinition',
            arguments: [$reflectionMethod]
        );
        /** @var ParameterDefinition $parameterDefinition */
        $parameterDefinition = $result[0];

        $this->assertIsArray($result);
        $this->assertInstanceOf(
            expected: ParameterDefinition::class,
            actual: $parameterDefinition
        );
        $this->assertTrue($parameterDefinition->allowsNull());
        $this->assertEquals(
            expected: $methodParameterName,
            actual: $parameterDefinition->getName()
        );
        $this->assertNull($parameterDefinition->getIdentifier());
        $this->assertEquals(
            expected: ParameterType::MinimalismFactories,
            actual: $parameterDefinition->getType()
        );
        $this->assertEquals(
            expected: $defaultValue,
            actual: $parameterDefinition->getDefaultValue()
        );
    }

    /**
     * @covers ::getMethodParametersDefinition
     * @return void
     */
    public function testItShouldGetMethodParametersDefinitionObjectFactory(
    ): void
    {
        $reflectionMethod = $this->createMock(ReflectionMethod::class);
        $reflectionParameter = $this->createMock(ReflectionParameter::class);
        $parameter = $this->createMock(ReflectionNamedType::class);
        $className = ObjectFactory::class;
        $methodParameterName = 'setObjectFactory';
        $defaultValue = 'default-value';
        $reflectionMethod->expects($this->once())
            ->method('getParameters')
            ->willReturn([$reflectionParameter]);
        $reflectionParameter->expects($this->once())
            ->method('getName')
            ->willReturn($methodParameterName);
        $reflectionParameter->expects($this->once())
            ->method('allowsNull')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('isDefaultValueAvailable')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('getDefaultValue')
            ->willReturn($defaultValue);
        $reflectionParameter->expects($this->once())
            ->method('getType')
            ->willReturn($parameter);
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $result = $this->invokeMethod(
            object: $this->factory,
            methodName: 'getMethodParametersDefinition',
            arguments: [$reflectionMethod]
        );
        /** @var ParameterDefinition $parameterDefinition */
        $parameterDefinition = $result[0];

        $this->assertIsArray($result);
        $this->assertInstanceOf(
            expected: ParameterDefinition::class,
            actual: $parameterDefinition
        );
        $this->assertTrue($parameterDefinition->allowsNull());
        $this->assertEquals(
            expected: $methodParameterName,
            actual: $parameterDefinition->getName()
        );
        $this->assertNull($parameterDefinition->getIdentifier());
        $this->assertEquals(
            expected: ParameterType::ObjectFactory,
            actual: $parameterDefinition->getType()
        );
        $this->assertEquals(
            expected: $defaultValue,
            actual: $parameterDefinition->getDefaultValue()
        );
    }

    /**
     * @covers ::getMethodParametersDefinition
     * @return void
     */
    public function testItShouldGetMethodParametersDefinitionSimpleObject(
    ): void
    {
        $reflectionMethod = $this->createMock(ReflectionMethod::class);
        $reflectionParameter = $this->createMock(ReflectionParameter::class);
        $parameter = $this->createMock(ReflectionNamedType::class);
        $className = SimpleObjectStub::class;
        $methodParameterName = 'setObjectFactory';
        $defaultValue = 'default-value';
        $reflectionMethod->expects($this->once())
            ->method('getParameters')
            ->willReturn([$reflectionParameter]);
        $reflectionParameter->expects($this->once())
            ->method('getName')
            ->willReturn($methodParameterName);
        $reflectionParameter->expects($this->once())
            ->method('allowsNull')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('isDefaultValueAvailable')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('getDefaultValue')
            ->willReturn($defaultValue);
        $reflectionParameter->expects($this->once())
            ->method('getType')
            ->willReturn($parameter);
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $result = $this->invokeMethod(
            object: $this->factory,
            methodName: 'getMethodParametersDefinition',
            arguments: [$reflectionMethod]
        );
        /** @var ParameterDefinition $parameterDefinition */
        $parameterDefinition = $result[0];

        $this->assertIsArray($result);
        $this->assertInstanceOf(
            expected: ParameterDefinition::class,
            actual: $parameterDefinition
        );
        $this->assertTrue($parameterDefinition->allowsNull());
        $this->assertEquals(
            expected: $methodParameterName,
            actual: $parameterDefinition->getName()
        );
        $this->assertEquals(
            expected: $className,
            actual: $parameterDefinition->getIdentifier()
        );        $this->assertEquals(
            expected: ParameterType::SimpleObject,
            actual: $parameterDefinition->getType()
        );
        $this->assertEquals(
            expected: $defaultValue,
            actual: $parameterDefinition->getDefaultValue()
        );
    }

    /**
     * @covers ::getMethodParametersDefinition
     * @return void
     */
    public function testItShouldGetMethodParametersDefinitionObject(
    ): void
    {
        $reflectionMethod = $this->createMock(ReflectionMethod::class);
        $reflectionParameter = $this->createMock(ReflectionParameter::class);
        $parameter = $this->createMock(ReflectionNamedType::class);
        $className = ObjectStub::class;
        $methodParameterName = 'setObjectFactory';
        $defaultValue = 'default-value';
        $reflectionMethod->expects($this->once())
            ->method('getParameters')
            ->willReturn([$reflectionParameter]);
        $reflectionParameter->expects($this->once())
            ->method('getName')
            ->willReturn($methodParameterName);
        $reflectionParameter->expects($this->once())
            ->method('allowsNull')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('isDefaultValueAvailable')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('getDefaultValue')
            ->willReturn($defaultValue);
        $reflectionParameter->expects($this->once())
            ->method('getType')
            ->willReturn($parameter);
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $result = $this->invokeMethod(
            object: $this->factory,
            methodName: 'getMethodParametersDefinition',
            arguments: [$reflectionMethod]
        );
        /** @var ParameterDefinition $parameterDefinition */
        $parameterDefinition = $result[0];

        $this->assertIsArray($result);
        $this->assertInstanceOf(
            expected: ParameterDefinition::class,
            actual: $parameterDefinition
        );
        $this->assertTrue($parameterDefinition->allowsNull());
        $this->assertEquals(
            expected: $methodParameterName,
            actual: $parameterDefinition->getName()
        );
        $this->assertEquals(
            expected: $className,
            actual: $parameterDefinition->getIdentifier()
        );
        $this->assertEquals(
            expected: ParameterType::Object,
            actual: $parameterDefinition->getType()
        );
        $this->assertEquals(
            expected: $defaultValue,
            actual: $parameterDefinition->getDefaultValue()
        );
    }

    /**
     * @covers ::getMethodParametersDefinition
     * @return void
     */
    public function testItShouldGetMethodParametersDefinitionPositionedParameter(
    ): void
    {
        $reflectionMethod = $this->createMock(ReflectionMethod::class);
        $reflectionParameter = $this->createMock(ReflectionParameter::class);
        $parameter = $this->createMock(ReflectionNamedType::class);
        $className = PositionedParameterStub::class;
        $methodParameterName = 'setObjectFactory';
        $defaultValue = 'default-value';
        $reflectionMethod->expects($this->once())
            ->method('getParameters')
            ->willReturn([$reflectionParameter]);
        $reflectionParameter->expects($this->once())
            ->method('getName')
            ->willReturn($methodParameterName);
        $reflectionParameter->expects($this->once())
            ->method('allowsNull')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('isDefaultValueAvailable')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('getDefaultValue')
            ->willReturn($defaultValue);
        $reflectionParameter->expects($this->once())
            ->method('getType')
            ->willReturn($parameter);
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $result = $this->invokeMethod(
            object: $this->factory,
            methodName: 'getMethodParametersDefinition',
            arguments: [$reflectionMethod]
        );
        /** @var ParameterDefinition $parameterDefinition */
        $parameterDefinition = $result[0];

        $this->assertIsArray($result);
        $this->assertInstanceOf(
            expected: ParameterDefinition::class,
            actual: $parameterDefinition
        );
        $this->assertTrue($parameterDefinition->allowsNull());
        $this->assertEquals(
            expected: $methodParameterName,
            actual: $parameterDefinition->getName()
        );
        $this->assertNull($parameterDefinition->getIdentifier());
        $this->assertEquals(
            expected: ParameterType::PositionedParameter,
            actual: $parameterDefinition->getType()
        );
        $this->assertEquals(
            expected: $defaultValue,
            actual: $parameterDefinition->getDefaultValue()
        );
    }

    /**
     * @covers ::getMethodParametersDefinition
     * @return void
     */
    public function testItShouldGetMethodParametersDefinitionParameter(
    ): void
    {
        $reflectionMethod = $this->createMock(ReflectionMethod::class);
        $reflectionParameter = $this->createMock(ReflectionParameter::class);
        $parameter = $this->createMock(ReflectionNamedType::class);
        $className = ParameterStub::class;
        $methodParameterName = 'setObjectFactory';
        $defaultValue = 'default-value';
        $reflectionMethod->expects($this->once())
            ->method('getParameters')
            ->willReturn([$reflectionParameter]);
        $reflectionParameter->expects($this->once())
            ->method('getName')
            ->willReturn($methodParameterName);
        $reflectionParameter->expects($this->once())
            ->method('allowsNull')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('isDefaultValueAvailable')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('getDefaultValue')
            ->willReturn($defaultValue);
        $reflectionParameter->expects($this->once())
            ->method('getType')
            ->willReturn($parameter);
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $result = $this->invokeMethod(
            object: $this->factory,
            methodName: 'getMethodParametersDefinition',
            arguments: [$reflectionMethod]
        );
        /** @var ParameterDefinition $parameterDefinition */
        $parameterDefinition = $result[0];

        $this->assertIsArray($result);
        $this->assertInstanceOf(
            expected: ParameterDefinition::class,
            actual: $parameterDefinition
        );
        $this->assertTrue($parameterDefinition->allowsNull());
        $this->assertEquals(
            expected: $methodParameterName,
            actual: $parameterDefinition->getName()
        );
        $this->assertNull($parameterDefinition->getIdentifier());
        $this->assertEquals(
            expected: ParameterType::Parameter,
            actual: $parameterDefinition->getType()
        );
        $this->assertEquals(
            expected: $defaultValue,
            actual: $parameterDefinition->getDefaultValue()
        );
    }

    /**
     * @covers ::getMethodParametersDefinition
     * @return void
     */
    public function testItShouldGetMethodParametersDefinitionModelParameter(
    ): void
    {
        $reflectionMethod = $this->createMock(ReflectionMethod::class);
        $reflectionParameter = $this->createMock(ReflectionParameter::class);
        $parameter = $this->createMock(ReflectionNamedType::class);
        $className = ModelParameters::class;
        $methodParameterName = 'setObjectFactory';
        $defaultValue = 'default-value';
        $reflectionMethod->expects($this->once())
            ->method('getParameters')
            ->willReturn([$reflectionParameter]);
        $reflectionParameter->expects($this->once())
            ->method('getName')
            ->willReturn($methodParameterName);
        $reflectionParameter->expects($this->once())
            ->method('allowsNull')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('isDefaultValueAvailable')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('getDefaultValue')
            ->willReturn($defaultValue);
        $reflectionParameter->expects($this->once())
            ->method('getType')
            ->willReturn($parameter);
        $parameter->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $result = $this->invokeMethod(
            object: $this->factory,
            methodName: 'getMethodParametersDefinition',
            arguments: [$reflectionMethod]
        );
        /** @var ParameterDefinition $parameterDefinition */
        $parameterDefinition = $result[0];

        $this->assertIsArray($result);
        $this->assertInstanceOf(
            expected: ParameterDefinition::class,
            actual: $parameterDefinition
        );
        $this->assertTrue($parameterDefinition->allowsNull());
        $this->assertEquals(
            expected: $methodParameterName,
            actual: $parameterDefinition->getName()
        );
        $this->assertNull($parameterDefinition->getIdentifier());
        $this->assertEquals(
            expected: ParameterType::ModelParameters,
            actual: $parameterDefinition->getType()
        );
        $this->assertEquals(
            expected: $defaultValue,
            actual: $parameterDefinition->getDefaultValue()
        );
    }

    /**
     * @covers ::getMethodParametersDefinition
     * @return void
     */
    public function testItShouldGetMethodParametersDefinitionDocument(
    ): void
    {
        $reflectionMethod = $this->createMock(ReflectionMethod::class);
        $reflectionParameter = $this->createMock(ReflectionParameter::class);
        $parameter = $this->createMock(ReflectionNamedType::class);
        $methodParameterName = Document::class;
        $reflectionMethod->expects($this->once())
            ->method('getParameters')
            ->willReturn([$reflectionParameter]);
        $reflectionParameter->expects($this->once())
            ->method('getName')
            ->willReturn($methodParameterName);
        $reflectionParameter->expects($this->once())
            ->method('allowsNull')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('isDefaultValueAvailable')
            ->willReturn(false);
        $reflectionParameter->expects($this->once())
            ->method('getType')
            ->willReturn($parameter);
        $parameter->expects($this->exactly(2))
            ->method('getName')
            ->willReturn(Document::class);

        $result = $this->invokeMethod(
            object: $this->factory,
            methodName: 'getMethodParametersDefinition',
            arguments: [$reflectionMethod]
        );
        /** @var ParameterDefinition $parameterDefinition */
        $parameterDefinition = $result[0];

        $this->assertIsArray($result);
        $this->assertInstanceOf(
            expected: ParameterDefinition::class,
            actual: $parameterDefinition
        );
        $this->assertTrue($parameterDefinition->allowsNull());
        $this->assertEquals(
            expected: $methodParameterName,
            actual: $parameterDefinition->getName()
        );
        $this->assertEmpty($parameterDefinition->getIdentifier());
        $this->assertEquals(
            expected: ParameterType::Document,
            actual: $parameterDefinition->getType()
        );
        $this->assertEquals(
            expected: null,
            actual: $parameterDefinition->getDefaultValue()
        );
    }

    /**
     * @covers ::getMethodParametersDefinition
     * @return void
     */
    public function testItShouldThroeException(
    ): void
    {
        $reflectionMethod = $this->createMock(ReflectionMethod::class);
        $reflectionParameter = $this->createMock(ReflectionParameter::class);
        $parameter = $this->createMock(ReflectionNamedType::class);
        $methodParameterName = ModelStub::class;
        $reflectionMethod->expects($this->once())
            ->method('getParameters')
            ->willReturn([$reflectionParameter]);
        $reflectionParameter->expects($this->once())
            ->method('getName')
            ->willReturn($methodParameterName);
        $reflectionParameter->expects($this->once())
            ->method('allowsNull')
            ->willReturn(true);
        $reflectionParameter->expects($this->once())
            ->method('isDefaultValueAvailable')
            ->willReturn(false);
        $reflectionParameter->expects($this->once())
            ->method('getType')
            ->willReturn($parameter);
        $parameter->expects($this->exactly(2))
            ->method('getName')
            ->willReturn(ModelStub::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Parameter type not supported');

        $result = $this->invokeMethod(
            object: $this->factory,
            methodName: 'getMethodParametersDefinition',
            arguments: [$reflectionMethod]
        );
    }

    /**
     * @covers ::generateMethodParametersValues
     * @return void
     */
    public function testItShouldGenerateMethodParametersValue(
    ): void
    {
        $objectFactory = $this->createMock(ObjectFactory::class);
        $methodParameterDefinition1 = $this->createMock(ParameterDefinition::class);
        $methodParameterDefinition2 = $this->createMock(ParameterDefinition::class);
        $parameterType1 = ParameterType::SimpleObject;
        $parameterType2 = ParameterType::MinimalismFactories;
        $methodParametersDefinition = [$methodParameterDefinition1, $methodParameterDefinition2];
        $parameters = $this->createMock(ModelParameters::class);
        $simpleObject = new SimpleObjectStub();
        $id = 'id-111';

        $methodParameterDefinition1->expects($this->once())
            ->method('getIdentifier')
            ->willReturn($id);
        $methodParameterDefinition1->expects($this->once())
            ->method('getName')
            ->willReturn(SimpleObjectStub::class);
        $methodParameterDefinition1->expects($this->once())
            ->method('getType')
            ->willReturn($parameterType1);
        $this->minimalismFactories->expects($this->once())
            ->method('getObjectFactory')
            ->willReturn($objectFactory);
        $objectFactory->expects($this->once())
            ->method('create')
            ->with($id, SimpleObjectStub::class, $parameters)
            ->willReturn($simpleObject);
        $methodParameterDefinition2->expects($this->once())
            ->method('getType')
            ->willReturn($parameterType2);

        $result = $this->factory->generateMethodParametersValues($methodParametersDefinition, $parameters);

        $this->assertEquals(
            expected: $result,
            actual: [$simpleObject, $this->minimalismFactories]
        );
    }
}