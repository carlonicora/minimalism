<?php

namespace CarloNicora\Minimalism\Tests\Unit\Services\ParameterValidator\Abstracts;

use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Abstracts\AbstractParameterValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Tests\Mocks\TestModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractParameterValidatorTest extends AbstractTestCase
{

    protected const IDENTIFIER = 'test';

    public function getInstance(): MockObject
    {
        return $this->getMockBuilder(AbstractParameterValidator::class)
            ->setConstructorArgs([$this->getServices()])
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
        $instance = $this->getInstance();
        /** @var MockObject|ModelInterface $modelMock */
        $modelMock = $this->getModelMock();

        $this->expectException(Exception::class);
        /** @noinspection PhpUndefinedMethodInspection */
        $instance->renderParameter(
            new ParameterObject(self::IDENTIFIER, ['required' => 1]),
            $modelMock,
            []
        );
    }


    public function testRenderParameterWithMatchingParameterObjectButNullValue()
    {
        $instance = $this->getInstance();
        /** @var MockObject|ModelInterface $modelMock */
        $modelMock = $this->getModelMock();

        $instance->expects(self::never())->method('setParameter');
        $modelMock->expects(self::once())->method('addReceivedParameters')->with(self::IDENTIFIER);

        /** @noinspection PhpUndefinedMethodInspection */
        $instance->renderParameter(
            new ParameterObject(self::IDENTIFIER, []),
            $modelMock,
            [self::IDENTIFIER => null]
        );
    }

    public function testRenderParameterWithMatchingParameterObjectNotNull()
    {
        $value = 'test value';
        $instance = $this->getInstance();
        /** @var MockObject|ModelInterface $modelMock */
        $modelMock = $this->getModelMock();

        $parameterObject = new ParameterObject(self::IDENTIFIER, []);
        $instance->expects(self::once())->method('setParameter')->with($parameterObject, $modelMock, $value);
        $modelMock->expects(self::once())->method('addReceivedParameters')->with(self::IDENTIFIER);

        /** @noinspection PhpUndefinedMethodInspection */
        $instance->renderParameter(
            $parameterObject,
            $modelMock,
            [self::IDENTIFIER => $value]
        );
    }

    public function testRenderParameterWithMatchingParameterObjectEncrypted(): void
    {
        $instance = $this->getInstance();
        /** @var MockObject|ModelInterface $modelMock */
        $modelMock = $this->getModelMock();

        $modelMock->expects(self::once())->method('setParameter');
        $modelMock->expects(self::once())->method('addReceivedParameters')->with(self::IDENTIFIER);
        $modelMock->expects(self::once())->method('decrypter');

        /** @noinspection PhpUndefinedMethodInspection */
        $instance->renderParameter(
            new ParameterObject(self::IDENTIFIER, ['encrypted' => 1]),
            $modelMock,
            [self::IDENTIFIER => 'value']
        );
    }
}
