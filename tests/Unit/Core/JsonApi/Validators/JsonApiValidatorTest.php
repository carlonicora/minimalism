<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\JsonApi\Validators;

use CarloNicora\Minimalism\Core\JsonApi\Validators\JsonApiValidator;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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

        $this->instance = new JsonApiValidator($this->getServices(), new ParameterObject('test', []));
    }

    /**
     * @dataProvider invalidTypes
     * @param $mixed
     * @throws \Exception
     */
    public function testSetParameterWithUnsupportedType($mixed)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('JsonApiValidator $parameter must be of type array');
        $this->instance->setParameter($this->mock, $mixed);
    }

    public function invalidTypes()
    {
        return [
            ['string'],
            [1],
            [true]
        ];
    }


    /**
     * @dataProvider validTypes
     * @param $mixed
     */
    public function testSetParameterWithSupportedTypes($mixed)
    {
        $this->mock->expects($this->once())->method('setParameter');
        $this->instance->setParameter($this->mock, $mixed);
    }

    public function validTypes()
    {
        return [
            [ [] ],
            [ null ]
        ];
    }
}
