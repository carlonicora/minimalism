<?php
namespace CarloNicora\Minimalism\Tests\Unit\Enums;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Enums\ParameterType;
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
        $this->expectExceptionCode(500);
        ParameterType::Null->getParameterValue(
            parameterDefinition: $this->mocker->createParameterDefinition(),
            minimalismFactories: $this->mocker->createMinimalismFactories(),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testServiceParameterValue(
    ): void
    {
        self::assertEquals(
            expected: $this->mocker->createServiceInterface(),
            actual: ParameterType::Service->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition(['getIdentifier' => 'className']),
                minimalismFactories: $this->mocker->createMinimalismFactories([
                    'getServiceFactory' => $this->mocker->createServiceFactory([
                        'create' => $this->mocker->createServiceInterface()
                    ])
                ]),
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
        self::assertEquals(
            expected: new PositionedParameter('value'),
            actual: ParameterType::PositionedParameter->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition(),
                minimalismFactories: $this->mocker->createMinimalismFactories(),
                parameters: $this->mocker->createModelParameters([
                    'getNextPositionedParameter' => 'value'
                ]),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testParameterValue(
    ): void
    {
        self::assertEquals(
            expected: 'value',
            actual: ParameterType::Parameter->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition([
                    'getName' => 'name'
                ]),
                minimalismFactories: $this->mocker->createMinimalismFactories(),
                parameters: $this->mocker->createModelParameters([
                    'getNamedParameter' => 'value'
                ]),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testFiles(
    ): void
    {
        self::assertEquals(
            expected: ['value'],
            actual: ParameterType::Simple->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition([
                    'getName' => 'files'
                ]),
                minimalismFactories: $this->mocker->createMinimalismFactories(),
                parameters: $this->mocker->createModelParameters([
                    'getFiles' => ['value']
                ]),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSimple(
    ): void
    {
        self::assertEquals(
            expected: 'value',
            actual: ParameterType::Simple->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition([
                    'getName' => 'name'
                ]),
                minimalismFactories: $this->mocker->createMinimalismFactories(),
                parameters: $this->mocker->createModelParameters([
                    'getNamedParameter' => 'value'
                ]),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDocument(
    ): void
    {
        $data = [
            'data' => [
                'type' => 'test',
                'id' => '1',
            ],
        ];

        self::assertEquals(
            expected: new Document($data),
            actual: ParameterType::Document->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition([
                    'getName' => 'name'
                ]),
                minimalismFactories: $this->mocker->createMinimalismFactories(),
                parameters: $this->mocker->createModelParameters([
                    'getNamedParameter' => $data
                ]),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testObject(
    ): void
    {
        self::assertEquals(
            expected: $this->mocker->createObjectInterface(),
            actual: ParameterType::Object->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition([
                    'getIdentifier' => 'identifier',
                    'getName' => 'name'
                ]),
                minimalismFactories: $this->mocker->createMinimalismFactories([
                    'getObjectFactory' => $this->mocker->createObjectFactory([
                        'create' => $this->mocker->createObjectInterface()
                    ])
                ]),
                parameters: $this->mocker->createModelParameters(),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSimpleObject(
    ): void
    {
        self::assertEquals(
            expected: $this->mocker->createSimpleObjectInterface(),
            actual: ParameterType::SimpleObject->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition([
                    'getIdentifier' => 'identifier',
                    'getName' => 'name'
                ]),
                minimalismFactories: $this->mocker->createMinimalismFactories([
                    'getObjectFactory' => $this->mocker->createObjectFactory([
                        'create' => $this->mocker->createSimpleObjectInterface()
                    ])
                ]),
                parameters: $this->mocker->createModelParameters(),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testMinimalismFactories(
    ): void
    {
        self::assertEquals(
            expected: $this->mocker->createMinimalismFactories(),
            actual: ParameterType::MinimalismFactories->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition(),
                minimalismFactories: $this->mocker->createMinimalismFactories(),
                parameters: $this->mocker->createModelParameters(),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testObjectFactory(
    ): void
    {
        self::assertEquals(
            expected: $this->mocker->createObjectFactory(),
            actual: ParameterType::ObjectFactory->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition(),
                minimalismFactories: $this->mocker->createMinimalismFactories([
                    'getObjectFactory' => $this->mocker->createObjectFactory(),
                ]),
                parameters: $this->mocker->createModelParameters(),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testParameter(
    ): void
    {
        self::assertNull(
            actual: ParameterType::ModelParameters->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition([
                    'allowsNull' => true
                ]),
                minimalismFactories: $this->mocker->createMinimalismFactories(),
            ),
        );

        self::assertEquals(
            expected: $this->mocker->createModelParameters(),
            actual: ParameterType::ModelParameters->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition(),
                minimalismFactories: $this->mocker->createMinimalismFactories(),
                parameters: $this->mocker->createModelParameters(),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDefaultValue(
    ): void
    {
        self::assertTrue(
            condition: ParameterType::ModelParameters->getParameterValue(
                parameterDefinition: $this->mocker->createParameterDefinition([
                    'getDefaultValue' => true
                ]),
                minimalismFactories: $this->mocker->createMinimalismFactories(),
            ),
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testMissingRequiredParameter(
    ): void
    {
        $this->expectExceptionCode(412);
        ParameterType::ModelParameters->getParameterValue(
            parameterDefinition: $this->mocker->createParameterDefinition([
                'allowsNull' => false
            ]),
            minimalismFactories: $this->mocker->createMinimalismFactories(),
        );
    }
}