<?php
namespace CarloNicora\Minimalism\Tests\Unit\Services\Logger\Traits;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\Logger\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\Logger\Traits\LoggerTrait;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

class LoggerTraitTest extends AbstractTestCase
{
    /** @var MockObject|LoggerTrait  */
    private ?MockObject $trait=null;

    public function setUp(): void
    {
        parent::setUp();

        $this->services = new ServicesFactory();
        $this->services->loadService(ServiceFactory::class);

        $this->trait = $this->getMockForTrait(LoggerTrait::class);
    }

    public function testMessageCreation() : void
    {
        $this->trait->loggerInitialise($this->services);

        $fileName = '/opt/project/data/logs/minimalism/tempfile.tmp';
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $this->trait->loggerWriteLog('message', $fileName);

        $this->assertFileExists($fileName);
    }

    public function testWriteError() : void
    {
        $this->trait->loggerInitialise($this->services);

        $fileName = '/opt/project/data/logs/minimalism/temp.error.log';
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $this->trait->loggerWriteError(1, 'message', 'temp', new Exception('msg', 1));

        $this->assertFileExists($fileName);
    }
}