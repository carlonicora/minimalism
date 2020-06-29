<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractWebModel;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class AbstractWebModelTest extends AbstractTestCase
{

    public function testGetViewNameWithDefault()
    {
        $instance = $this->getMockBuilder(AbstractWebModel::class)
            ->setConstructorArgs([$this->getServices()])
            ->getMockForAbstractClass();

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEmpty($instance->getViewName());
    }
}
