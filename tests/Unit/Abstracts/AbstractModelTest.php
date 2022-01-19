<?php

namespace CarloNicora\Minimalism\Tests\Unit\Abstracts;

use CarloNicora\JsonApi\Document;
use CarloNicora\Minimalism\Abstracts\AbstractModel;
use CarloNicora\Minimalism\Enums\HttpCode;
use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ModelFactory;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Services\Path;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;

/**
 * Class AbstractModelTest
 * @package CarloNicora\Minimalism\Tests\Unit\Abstracts
 * @coversDefaultClass \CarloNicora\Minimalism\Abstracts\AbstractModel
 */
class AbstractModelTest extends AbstractTestCase
{
    private AbstractModel $model;
    private MinimalismFactories $minimalismFactories;

    public function setUp(): void
    {
        parent::setUp();

        $this->minimalismFactories = $this->createMock(MinimalismFactories::class);
        $this->model = new AbstractModel($this->minimalismFactories, 'getMinimalism');
    }

    /**
     * @covers ::__construct
     * @return void
     */
    public function testCreateModelWithDefinedFunction(
    ): void
    {
        $minimalismFactories = $this->createMock(MinimalismFactories::class);
        $objectFactory = $this->createMock(ObjectFactory::class);

        $minimalismFactories->expects($this->once())
            ->method('getObjectFactory')
            ->willReturn($objectFactory);

        $model = new AbstractModel($minimalismFactories, 'getMinimalism');

        $this->assertSame(
            expected: $objectFactory,
            actual: $this->getProperty(
                object: $model,
                parameterName: 'objectFactory'
            )
        );
        $this->assertEquals(
            expected: 'getMinimalism',
            actual: $this->getProperty(
                object: $model,
                parameterName: 'function'
            )
        );
        $this->assertDefaultParamsCreated($model);
    }

    /**
     * @covers ::__construct
     * @return void
     */
    public function testCreateModelInCliMode(
    ): void
    {
        $minimalismFactories = $this->createMock(MinimalismFactories::class);
        $objectFactory = $this->createMock(ObjectFactory::class);
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);

        $minimalismFactories->expects($this->once())
            ->method('getObjectFactory')
            ->willReturn($objectFactory);
        $minimalismFactories->expects($this->once())
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getUrl')
            ->willReturn(null);

        $model = new AbstractModel($minimalismFactories);

        $this->assertSame(
            expected: $objectFactory,
            actual: $this->getProperty(
                object: $model,
                parameterName: 'objectFactory'
            )
        );
        $this->assertEquals(
            expected: 'cli',
            actual: $this->getProperty(
                object: $model,
                parameterName: 'function'
            )
        );
        $this->assertDefaultParamsCreated($model);
    }

    /**
     * @covers ::__construct
     * @return void
     */
    public function testCreateModelWithNotDefinedRequestMethod(
    ): void
    {
        $minimalismFactories = $this->createMock(MinimalismFactories::class);
        $objectFactory = $this->createMock(ObjectFactory::class);
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);

        $minimalismFactories->expects($this->once())
            ->method('getObjectFactory')
            ->willReturn($objectFactory);
        $minimalismFactories->expects($this->once())
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://minimalism');

        $model = new AbstractModel($minimalismFactories);

        $this->assertSame(
            expected: $objectFactory,
            actual: $this->getProperty(
                object: $model,
                parameterName: 'objectFactory'
            )
        );
        $this->assertEquals(
            expected: 'get',
            actual: $this->getProperty(
                object: $model,
                parameterName: 'function'
            )
        );
        $this->assertDefaultParamsCreated($model);
    }

    /**
     * @covers ::__construct
     * @return void
     */
    public function testCreateModelWithPostMethod(
    ): void
    {
        $minimalismFactories = $this->createMock(MinimalismFactories::class);
        $objectFactory = $this->createMock(ObjectFactory::class);
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $minimalismFactories->expects($this->once())
            ->method('getObjectFactory')
            ->willReturn($objectFactory);
        $minimalismFactories->expects($this->once())
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://minimalism');

        $model = new AbstractModel($minimalismFactories);

        $this->assertSame(
            expected: $objectFactory,
            actual: $this->getProperty(
                object: $model,
                parameterName: 'objectFactory'
            )
        );
        $this->assertEquals(
            expected: 'post',
            actual: $this->getProperty(
                object: $model,
                parameterName: 'function'
            )
        );
        $this->assertDefaultParamsCreated($model);

        unset($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @param string $method
     * @param string $function
     * @covers ::__construct
     * @dataProvider requestMethodsDataProvider
     * @return void
     */
    public function testCreateModelWithXMethod(
        string $method,
        string $function
    ): void
    {
        $minimalismFactories = $this->createMock(MinimalismFactories::class);
        $objectFactory = $this->createMock(ObjectFactory::class);
        $serviceFactory = $this->createMock(ServiceFactory::class);
        $path = $this->createMock(Path::class);
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_HTTP_METHOD'] = $method;

        $minimalismFactories->expects($this->once())
            ->method('getObjectFactory')
            ->willReturn($objectFactory);
        $minimalismFactories->expects($this->once())
            ->method('getServiceFactory')
            ->willReturn($serviceFactory);
        $serviceFactory->expects($this->once())
            ->method('getPath')
            ->willReturn($path);
        $path->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://minimalism');

        $model = new AbstractModel($minimalismFactories);

        $this->assertSame(
            expected: $objectFactory,
            actual: $this->getProperty(
                object: $model,
                parameterName: 'objectFactory'
            )
        );
        $this->assertEquals(
            expected: $function,
            actual: $this->getProperty(
                object: $model,
                parameterName: 'function'
            )
        );
        $this->assertDefaultParamsCreated($model);

        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['HTTP_X_HTTP_METHOD']);
    }

    /**
     * @covers ::setParameters
     * @return void
     */
    public function testItShouldSetParameters(
    ): void
    {
        $parameters = new ModelParameters();
        $this->model->setParameters($parameters);

        $this->assertSame(
            expected: $parameters,
            actual: $this->getProperty(
                $this->model,
                'parameters'
            )
        );
    }

    /**
     * @covers ::getDocument
     * @return void
     */
    public function testItShouldGetDocument(
    ): void
    {
        $this->assertInstanceOf(Document::class, $this->model->getDocument());
    }

    /**
     * @covers ::getView
     * @return void
     */
    public function testItShouldGetView(
    ): void
    {
        $view = 'someView';

        $this->assertNull($this->model->getView());

        $this->setProperty(
            object: $this->model,
            parameterName: 'view',
            parameterValue: $view,
        );

        $this->assertEquals(
            expected: $view,
            actual: $this->model->getView()
        );
    }

    /**
     * @covers ::getRedirection
     * @return void
     */
    public function testItShouldGetRedirection(
    ): void
    {
        $redirection = 'someRedirection';

        $this->assertNull($this->model->getRedirection());

        $this->setProperty(
            object: $this->model,
            parameterName: 'redirection',
            parameterValue: $redirection,
        );

        $this->assertSame(
            expected: $redirection,
            actual: $this->model->getRedirection()
        );
    }

    /**
     * @covers ::getRedirectionParameters
     * @return void
     */
    public function testItShouldGetRedirectionParameters(
    ): void
    {
        $redirection = new ModelParameters();

        $this->assertNull($this->model->getRedirectionParameters());

        $this->setProperty(
            object: $this->model,
            parameterName: 'redirectionParameters',
            parameterValue: $redirection,
        );

        $this->assertSame(
            expected: $redirection,
            actual: $this->model->getRedirectionParameters()
        );
    }

    /**
     * @covers ::getRedirectionFunction
     * @return void
     */
    public function testItShouldGetRedirectionFunction(
    ): void
    {
        $this->assertSame(
            expected: 'getMinimalism',
            actual: $this->model->getRedirectionFunction()
        );
    }

    /**
     * @covers ::run
     * @return void
     */
    public function testItShouldRun(
    ): void
    {
        $minimalismFactories = $this->createMock(MinimalismFactories::class);
        $functionName = 'testFunction';
        $modelFactory = $this->createMock(ModelFactory::class);
        $modelParameters = $this->createMock(ModelParameters::class);
        $parametersDefinitions = [];
        $parametersValues = ['param1', 123];
        $result = HttpCode::Accepted;
        $model = $this->getMockBuilder(AbstractModel::class)
            ->setConstructorArgs([$minimalismFactories, $functionName])
            ->addMethods(['testFunction'])
            ->getMock();
        $model->setParameters($modelParameters);

        $minimalismFactories->expects($this->exactly(2))
            ->method('getModelFactory')
            ->willReturn($modelFactory);
        $modelFactory->expects($this->once())
            ->method('getModelMethodParametersDefinition')
            ->with($model::class, $functionName)
            ->willReturn($parametersDefinitions);
        $modelFactory->expects($this->once())
            ->method('generateMethodParametersValues')
            ->with($parametersDefinitions, $modelParameters)
            ->willReturn($parametersValues);
        $model->expects($this->once())
            ->method($functionName)
            ->with(...$parametersValues)
            ->willReturn($result);

        $this->assertEquals(
            expected: $result,
            actual: $model->run()
        );
    }

    /**
     * @covers ::getParameterValue
     * @return void
     */
    public function testItShouldGetParameterValue(
    ): void
    {
        $parameters = $this->createMock(ModelParameters::class);
        $parameterName = 'paramName';
        $parameterValue = 'paramValue';
        $this->model->setParameters($parameters);

        $parameters->expects($this->once())
            ->method('getNamedParameter')
            ->with($parameterName)
            ->willReturn($parameterValue);

        $this->assertSame(
            expected: $parameterValue,
            actual: $this->model->getParameterValue($parameterName)
        );
    }

    /**
     * @return array
     */
    public function requestMethodsDataProvider(
    ): array
    {
        return [
            ['DELETE', 'delete'],
            ['PUT', 'put'],
            ['PATCH', 'patch']
        ];
    }

    private function assertDefaultParamsCreated(
        AbstractModel $model
    ): void
    {
        $this->assertEquals(
            expected: new Document(),
            actual: $model->getDocument()
        );
        $this->assertEquals(
            expected: new ModelParameters(),
            actual: $this->getProperty(
                object: $model,
                parameterName: 'parameters'
            )
        );
    }
}