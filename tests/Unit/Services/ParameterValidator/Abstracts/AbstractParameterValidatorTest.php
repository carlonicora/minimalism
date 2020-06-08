<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Abstracts;

use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractParameterValidatorTest extends AbstractTestCase
{

    protected const IDENTIFIER = 'test';

    /**
     * @param $parameterObjectParameter
     * @return AbstractParameterValidator
     */
    public function getInstance($parameterObjectParameter = []): MockObject
    {
        return $this->getMockBuilder(AbstractParameterValidator::class)
            ->setConstructorArgs([
                $this->getServices(),
                new ParameterObject(self::IDENTIFIER, $parameterObjectParameter)
            ])
            ->onlyMethods(['setParameter'])
            ->getMockForAbstractClass();
    }

    public function getModelMock(): MockObject
    {
        return $this->getMockBuilder(TestModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->onlyMethods(['addReceivedParameters', 'setParameter', 'decrypter'])
            ->getMock();
    }

    public function testRenderParameterWithoutMatchingRequiredParameterObject()
    {
        $instance = $this->getInstance(['required' => 1]);
        $modelMock = $this->getModelMock();

        $this->expectException(\Exception::class);
        $instance->renderParameter($modelMock, []);
    }


    public function testRenderParameterWithMatchingParameterObjectButNullValue()
    {
        $instance = $this->getInstance();
        $modelMock = $this->getModelMock();

        $instance->expects($this->never())->method('setParameter');
        $modelMock->expects($this->once())->method('addReceivedParameters')->with(self::IDENTIFIER);

        $instance->renderParameter($modelMock, [self::IDENTIFIER => null]);
    }

    public function testRenderParameterWithMatchingParameterObjectNotNull()
    {
        $value = 'test value';
        $instance = $this->getInstance();
        $modelMock = $this->getModelMock();

        $instance->expects($this->once())->method('setParameter')->with($modelMock, $value);
        $modelMock->expects($this->once())->method('addReceivedParameters')->with(self::IDENTIFIER);

        $instance->renderParameter($modelMock, [self::IDENTIFIER => $value]);
    }

    public function testRenderParameterWithMatchingParameterObjectEncrypted()
    {
        $instance = $this->getInstance(['encrypted' => 1]);
        $modelMock = $this->getModelMock();

        $modelMock->expects($this->once())->method('setParameter');
        $modelMock->expects($this->once())->method('addReceivedParameters')->with(self::IDENTIFIER);
        $modelMock->expects($this->once())->method('decrypter');

        $instance->renderParameter($modelMock, [self::IDENTIFIER => 'value']);
    }
}
