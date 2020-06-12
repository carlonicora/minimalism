<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\JsonApi\Validators;

use CarloNicora\Minimalism\Core\JsonApi\Validators\JsonApiValidator;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TypeError;

class JsonApiValidatorTest extends AbstractTestCase
{

    /** @var ModelInterface */
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
     */
    public function testSetParameterWithSupportedTypes($mixed)
    {
        $this->mock->expects($this->once())->method('setParameter');
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
