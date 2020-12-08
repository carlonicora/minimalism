<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractApiModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;
use InvalidArgumentException;

class AbstractApiModelTest extends AbstractTestCase
{

    /** @noinspection PhpUndefinedMethodInspection */
    public function testRequiresAuthVerbs()
    {
        $instance = $this->getMockBuilder(AbstractApiModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        self::assertFalse($instance->requiresAuth('DELETE'));
        self::assertFalse($instance->requiresAuth('GET'));
        self::assertFalse($instance->requiresAuth('POST'));
        self::assertFalse($instance->requiresAuth('PUT'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP verb not supported');
        self::assertFalse($instance->requiresAuth('PATCH'));
    }


    public function testGetParameters()
    {
        $instance = $this->getMockBuilder(AbstractApiModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        /** @noinspection PhpUndefinedMethodInspection */
        self::assertEmpty($instance->getParameters());
    }


    /** @noinspection PhpUndefinedFieldInspection */
    public function testSetVerb()
    {
        $instance = $this->getMockBuilder(AbstractApiModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        self::assertEquals('GET', $instance->verb);

        /** @noinspection PhpUndefinedMethodInspection */
        $instance->setVerb('POST');
        self::assertEquals('POST', $instance->verb);
    }
}
