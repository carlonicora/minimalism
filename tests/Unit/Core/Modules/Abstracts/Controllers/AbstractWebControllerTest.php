<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Controllers;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Controllers\AbstractWebController;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractWebControllerTest extends AbstractTestCase
{
    /**
     * @var MockObject|AbstractWebController
     */
    protected MockObject $controller;

    public function setUp(): void
    {
        parent::setUp();

        $this->controller = $this->getMockForAbstractClass(AbstractWebController::class, [$this->services]);
    }

    public function testPreRender() : void
    {
        $this->controller->preRender();

        $this->assertEquals(1,1);
    }
}