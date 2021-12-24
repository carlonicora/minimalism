<?php
namespace CarloNicora\Minimalism\Tests\Unit\Enums;

use CarloNicora\Minimalism\Enums\ParameterType;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Objects\ParameterDefinition;
use CarloNicora\Minimalism\Parameters\PositionedParameter;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;
use Exception;

class ParameterTypeTest extends AbstractTestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testNullParameterValue(
    ): void
    {
        $parameterDefinition = $this->getMockBuilder(ParameterDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minimalismFactories = $this->getMockBuilder(MinimalismFactories::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->expectExceptionCode(500);
        ParameterType::Null->getParameterValue(
            parameterDefinition: $parameterDefinition,
            minimalismFactories: $minimalismFactories,
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testServiceParameterValue(
    ): void
    {
        $serviceInterface = $this->getMockBuilder(ServiceInterface::class)
            ->disableOriginalClone()
            ->getMock();

        $parameterDefinition = $this->getMockBuilder(ParameterDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parameterDefinition->method('getIdentifier')->willReturn('className');

        $serviceFactory = $this->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceFactory->method('create')->willReturn($serviceInterface);

        $minimalismFactories = $this->getMockBuilder(MinimalismFactories::class)
            ->disableOriginalConstructor()
            ->getMock();
        $minimalismFactories->method('getServiceFactory')->willReturn($serviceFactory);

        self::assertEquals(
            expected: $serviceInterface,
            actual: ParameterType::Service->getParameterValue(
                parameterDefinition: $parameterDefinition,
                minimalismFactories: $minimalismFactories,
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testPositionedParameterValue(
    ): void
    {
        $parameterDefinition = $this->getMockBuilder(ParameterDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $minimalismFactories = $this->getMockBuilder(MinimalismFactories::class)
            ->disableOriginalConstructor()
            ->getMock();

        $parameters = $this->getMockBuilder(ModelParameters::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parameters->method('getNextPositionedParameter')->willReturn('value');

        self::assertEquals(
            expected: new PositionedParameter('value'),
            actual: ParameterType::PositionedParameter->getParameterValue(
                parameterDefinition: $parameterDefinition,
                minimalismFactories: $minimalismFactories,
                parameters: $parameters,
            ),
        );
    }
}