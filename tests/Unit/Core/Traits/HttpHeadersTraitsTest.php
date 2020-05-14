<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Traits;

use CarloNicora\Minimalism\Core\Traits\HttpHeadersTrait;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class HttpHeadersTraitsTest extends AbstractTestCase
{
    public function testGetHeaders() : void
    {
        /** @var MockObject|HttpHeadersTrait $trait */
        $trait = $this->getMockForTrait(HttpHeadersTrait::class);

        $this->assertNull($trait->getHeader('null'));
    }
}