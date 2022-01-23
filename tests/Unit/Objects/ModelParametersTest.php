<?php

namespace CarloNicora\Minimalism\Tests\Unit\Objects;

use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;

/**
 * Class ModelParametersTest
 * @package CarloNicora\Minimalism\Tests\Unit\Objects
 * @coversDefaultClass \CarloNicora\Minimalism\Objects\ModelParameters
 */
class ModelParametersTest extends AbstractTestCase
{
    private ModelParameters $modelParameters;

    public function setUp(
    ): void
    {
        parent::setUp();
        $this->modelParameters = new ModelParameters();
    }

    /**
     * @covers ::__construct
     * @covers ::addNamedParameter
     * @covers ::getNamedParameter
     * @return void
     */
    public function testNamedParameter(
    ): void
    {
        $name1 = 'paramName1';
        $value1 = 'paramValue1';
        $name2 = 'paramName1';
        $value2 = 'paramValue1';

        $this->modelParameters->addNamedParameter($name1, $value1);
        $this->modelParameters->addNamedParameter($name2, $value2);

        $this->assertEquals(
            expected: $value1,
            actual: $this->modelParameters->getNamedParameter($name1)
        );
        $this->assertEquals(
            expected: $value2,
            actual: $this->modelParameters->getNamedParameter($name2)
        );
    }

    /**
     * @covers ::addFile
     * @covers ::getFile
     * @return void
     */
    public function testFile(
    ): void
    {
        $fileName1 = 'fileName1';
        $file1 = ['file1'];
        $fileName2 = 'fileName2';
        $file2 = ['file2'];

        $this->modelParameters->addFile($fileName1, $file1);
        $this->modelParameters->addFile($fileName2, $file2);

        $this->assertEquals(
            expected: $file1,
            actual: $this->modelParameters->getFile($fileName1)
        );
        $this->assertEquals(
            expected: $file2,
            actual: $this->modelParameters->getFile($fileName2)
        );
    }

    /**
     * @covers ::getFiles
     * @return void
     */
    public function testItShouldGetFiles(
    ): void
    {
        $fileName1 = 'fileName1';
        $file1 = ['file1'];
        $fileName2 = 'fileName2';
        $file2 = ['file2'];

        $this->modelParameters->addFile($fileName1, $file1);
        $this->modelParameters->addFile($fileName2, $file2);

        $this->assertEquals(
            expected: [$fileName1 => $file1, $fileName2 => $file2],
            actual: $this->modelParameters->getFiles()
        );
    }

    /**
     * @covers ::addPositionedParameter
     * @covers ::getNextPositionedParameter
     * @return void
     */
    public function testPositionedParameter(
    ): void
    {
        $value1 = 'paramValue1';
        $value2 = 'paramValue2';
        $this->modelParameters->addPositionedParameter($value1);
        $this->modelParameters->addPositionedParameter($value2);

        $this->assertEquals(
            expected: $value1,
            actual: $this->modelParameters->getNextPositionedParameter()
        );
        $this->assertEquals(
            expected: $value2,
            actual: $this->modelParameters->getNextPositionedParameter()
        );
        $this->assertNull(
            $this->modelParameters->getNextPositionedParameter()
        );
    }
}