<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractWebModel;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;

class AbstractWebModelTest extends AbstractTestCase
{
    public function testViewName() : void
    {
        $mock = $this->getMockForAbstractClass(AbstractWebModel::class, [$this->services]);

        $this->setProperty($mock, 'viewName', 'pippo');
        $this->assertEquals('pippo', $mock->getViewName());
    }
}