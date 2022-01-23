<?php

namespace CarloNicora\Minimalism\Tests\Unit\Builders;

use CarloNicora\Minimalism\Builders\ModelBuilder;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;
use CarloNicora\Minimalism\Tests\Stubs\ModelStub;
use RuntimeException;

/**
 * Class ModelBuilderTest
 * @package CarloNicora\Minimalism\Tests\Unit\Builders
 * @coversDefaultClass \CarloNicora\Minimalism\Builders\ModelBuilder
 */
class ModelBuilderTest extends AbstractTestCase
{
    private ModelBuilder $modelBuilder;

    /**
     * @return void
     */
    public function setUp(
    ): void
    {
        parent::setUp();

        $this->modelBuilder = new ModelBuilder([], [], []);
    }

    /**
     * @covers ::getModelClass
     * @return void
     */
    public function testItShouldGetModelClass(
    ): void
    {
        $modelClass = ModelStub::class;
        $this->setProperty(
            object: $this->modelBuilder,
            parameterName: 'modelClass',
            parameterValue: $modelClass
        );

        $this->assertEquals(
            expected: $modelClass,
            actual: $this->modelBuilder->getModelClass()
        );
    }

    /**
     * @covers ::getModelClass
     * @return void
     */
    public function testItShouldGetModelClassWithException(
    ): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Model not found');

        $this->modelBuilder->getModelClass();
    }

    /**
     * @covers ::getParameters
     * @return void
     */
    public function testItShouldGetParameters(
    ): void
    {
        $modelParameters = ['param1', 'param2'];
        $this->setProperty(
            object: $this->modelBuilder,
            parameterName: 'modelClass',
            parameterValue:  ModelStub::class
        );
        $this->setProperty(
            object: $this->modelBuilder,
            parameterName: 'modelParameters',
            parameterValue:  $modelParameters
        );

        $this->assertEquals(
            expected: $modelParameters,
            actual: $this->modelBuilder->getParameters()
        );
    }

    /**
     * @covers ::getParameters
     * @return void
     */
    public function testItShouldGetParametersWithException(
    ): void
    {
        $this->setProperty(
            object: $this->modelBuilder,
            parameterName: 'modelParameters',
            parameterValue: []
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Model not found');

        $this->modelBuilder->getParameters();
    }

    /**
     * @covers ::getRemainingParameters
     * @return void
     */
    public function testItShouldGetRemainingParametersWithDefaultSkip(
    ): void
    {
        $parameters = ['param0', 'param1', 'param2'];

        $result = $this->invokeMethod(
            object: $this->modelBuilder,
            methodName: 'getRemainingParameters',
            arguments: [$parameters],
        );

        $this->assertEquals(
            expected: ['param1', 'param2'],
            actual: $result
        );
    }

    /**
     * @covers ::getRemainingParameters
     * @return void
     */
    public function testItShouldGetRemainingParameters(
    ): void
    {
        $parameters = ['param0', 'param1', 'param2', 'param3', 'param4'];

        $result = $this->invokeMethod(
            object: $this->modelBuilder,
            methodName: 'getRemainingParameters',
            arguments: [$parameters, 2],
        );

        $this->assertEquals(
            expected: ['param3', 'param4'],
            actual: $result
        );
    }

    /**
     * @return void
     */
    public function testItShouldCheckDoesFolderExistTrue(
    ): void
    {
        $this->assertTrue(
            $this->invokeMethod(
                object: $this->modelBuilder,
                methodName: 'doesFolderExist',
                arguments: ['Models', ['models-folder' => ModelStub::class]],
            )
        );
    }

    /**
     * @return void
     */
    public function testItShouldCheckDoesFolderExistFalse(
    ): void
    {
        $this->assertFalse(
            $this->invokeMethod(
                object: $this->modelBuilder,
                methodName: 'doesFolderExist',
                arguments: ['Models', ['models2-folder' => ModelStub::class]],
            )
        );
    }

    /**
     * @covers ::doesModelExist
     * @return void
     */
    public function testItShouldCheckDoesModelExistsTrue(
    ): void
    {
        $this->assertTrue(
            $this->invokeMethod(
                object: $this->modelBuilder,
                methodName: 'doesModelExist',
                arguments: ['Some-Model', ['somemodel' => []]],
            )
        );
    }

    /**
     * @covers ::doesModelExist
     * @return void
     */
    public function testItShouldCheckDoesModelExistsFalse(
    ): void
    {
        $this->assertFalse(
            $this->invokeMethod(
                object: $this->modelBuilder,
                methodName: 'doesModelExist',
                arguments: ['Some-Model', []],
            )
        );
    }

    /**
     * @covers ::getProperFolderName
     * @return void
     */
    public function testItShouldGetProperFolderName(
    ): void
    {
        $result = $this->invokeMethod(
            object: $this->modelBuilder,
            methodName: 'getProperFolderName',
            arguments: ['MyOwn-folderName'],
        );

        $this->assertEquals(
            expected: 'myown-foldername-folder',
            actual: $result
        );
    }

    /**
     * @covers ::getProperModelName
     * @return void
     */
    public function testItShouldGetProperModelName(
    ): void
    {
        $result = $this->invokeMethod(
            object: $this->modelBuilder,
            methodName: 'getProperModelName',
            arguments: ['Post-Model'],
        );

        $this->assertEquals(
            expected: 'postmodel',
            actual: $result
        );
    }
}