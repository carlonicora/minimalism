<?php

namespace CarloNicora\Minimalism\Tests\Unit\Objects;

use CarloNicora\Minimalism\Enums\ParameterType;
use CarloNicora\Minimalism\Objects\ParameterDefinition;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;

/**
 * Class ParameterDefinitionTest
 * @package CarloNicora\Minimalism\Tests\Unit\Objects
 * @coversDefaultClass \CarloNicora\Minimalism\Objects\ParameterDefinition
 */
class ParameterDefinitionTest extends AbstractTestCase
{
    private ParameterDefinition $parameterDefinition;

    public function setUp(
    ): void
    {
        parent::setUp();

        $this->parameterDefinition = new ParameterDefinition('someName', true, 'someDefault');
    }

    /**
     * @covers ::__construct
     * @covers ::setIdentifier
     * @covers ::getIdentifier
     * @return void
     */
    public function testItShouldTestIdentifier(
    ): void
    {
        $identifier = '12345';

        $this->assertNull($this->parameterDefinition->getIdentifier());

        $this->parameterDefinition->setIdentifier($identifier);

        $this->assertEquals(
            expected: $identifier,
            actual: $this->parameterDefinition->getIdentifier()
        );
    }

    /**
     * @covers ::getDefaultValue
     * @return void
     */
    public function testItShouldGetDefaultValue(
    ): void
    {
        $this->assertEquals(
            expected: 'someDefault',
            actual: $this->parameterDefinition->getDefaultValue()
        );
    }

    /**
     * @covers ::setIsPositionedParameter
     * @covers ::isPositionedParameter
     * @return void
     */
    public function testItShouldTestIsPositionedParameter(
    ): void
    {
        $this->assertFalse($this->parameterDefinition->isPositionedParameter());

        $this->parameterDefinition->setIsPositionedParameter(true);

        $this->assertTrue($this->parameterDefinition->isPositionedParameter());
    }

    /**
     * @covers ::setType
     * @covers ::getType
     * @return void
     */
    public function testItShouldTestType(
    ): void
    {
        $this->assertEquals(
            expected: ParameterType::Null,
            actual: $this->parameterDefinition->getType()
        );

        $this->parameterDefinition->setType(ParameterType::Document);

        $this->assertEquals(
            expected: ParameterType::Document,
            actual: $this->parameterDefinition->getType()
        );
    }

    /**
     * @covers ::getName
     * @return void
     */
    public function testItShouldTestGetName(
    ): void
    {
        $this->assertEquals(
            expected: 'someName',
            actual: $this->parameterDefinition->getName()
        );
    }

    /**
     * @covers ::allowsNull
     * @return void
     */
    public function testItShouldTestAllowsNull(
    ): void
    {
        $this->assertTrue($this->parameterDefinition->allowsNull());
    }
}