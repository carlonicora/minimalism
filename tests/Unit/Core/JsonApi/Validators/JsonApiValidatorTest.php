<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\JsonApi\Validators;

use CarloNicora\Minimalism\Core\JsonApi\Validators\JsonApiValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use TypeError;

class JsonApiValidatorTest extends AbstractTestCase
{

    private MockObject $mock;
    private JsonApiValidator $instance;

    public function setUp(): void
    {
        $this->mock = $this->getMockBuilder(TestModel::class)
            ->onlyMethods(['setParameter'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = new JsonApiValidator($this->getServices()/*, new ParameterObject('test', [])*/);
    }

    /**
     * @dataProvider invalidTypes
     * @param $mixed
     * @throws Exception
     */
    public function testSetParameterWithUnsupportedType($mixed)
    {
        $this->expectException(TypeError::class);
        /** @noinspection PhpParamsInspection */
        $this->instance->setParameter(new ParameterObject('test', []), $this->mock, $mixed);
    }

    public function invalidTypes()
    {
        return [
            ['string'],
            [1],
            [true],
        ];
    }


    /**
     * @dataProvider validTypes
     * @param $mixed
     * @throws Exception
     */
    public function testSetParameterWithSupportedTypes($mixed)
    {
        $this->mock->expects(self::once())->method('setParameter');
        /** @noinspection PhpParamsInspection */
        $this->instance->setParameter(new ParameterObject('test', []), $this->mock, $mixed);
    }

    public function validTypes()
    {
        return [
            [ [] ],
            [ null ]
        ];
    }
}
