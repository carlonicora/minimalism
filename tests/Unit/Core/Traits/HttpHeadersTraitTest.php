<?php

namespace CarloNicora\Minimalism\Tests\Unit\Core\Traits;

use CarloNicora\Minimalism\Core\Traits\HttpHeadersTrait;
use CarloNicora\Minimalism\Tests\Unit\AbstractTestCase;

class HttpHeadersTraitTest extends AbstractTestCase
{

    public function testGetHeader()
    {
        global $_SERVER;
        $instance = $this->getMockBuilder(HttpHeadersTrait::class)->getMockForTrait();

        $_SERVER['HTTP_ACCEPT'] = 'text/html';
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'UTF-8';

        $this->assertEquals('text/html', $instance->getHeader('Accept'));
        $this->assertEquals('UTF-8', $instance->getHeader('Accept-Encoding'));

        unset($_SERVER['HTTP_ACCEPT']);
        unset($_SERVER['HTTP_ACCEPT_ENCODING']);
    }
}