<?php
namespace CarloNicora\Minimalism\Tests\Unit;

use CarloNicora\Minimalism\Core\Bootstrapper;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;

class BootstrapperTest extends AbstractTestCase
{
    /**
     *
     */
    public function testFailToInitialiseBootstrapperBecauseLackingConfigurationFile() : Bootstrapper
    {
        $this->expectExceptionCode(ConfigurationException::ERROR_CONFIGURATION_FILE_ERROR);
        return new Bootstrapper();
    }

    public function testMe() : Bootstrapper
    {
        $this->expectExceptionCode(ConfigurationException::ERROR_CONFIGURATION_FILE_ERROR);
        return new Bootstrapper();
    }
}