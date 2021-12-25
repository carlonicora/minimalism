<?php
namespace CarloNicora\Minimalism\Tests\Factories;

use CarloNicora\Minimalism\Factories\MinimalismFactories;
use CarloNicora\Minimalism\Factories\ObjectFactory;
use CarloNicora\Minimalism\Factories\ServiceFactory;
use CarloNicora\Minimalism\Interfaces\ObjectInterface;
use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use CarloNicora\Minimalism\Interfaces\SimpleObjectInterface;
use CarloNicora\Minimalism\Objects\ModelParameters;
use CarloNicora\Minimalism\Objects\ParameterDefinition;
use CarloNicora\Minimalism\Tests\Abstracts\AbstractTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class MocksFactory
{
    /**
     * @param AbstractTestCase $test
     */
    public function __construct(
        private AbstractTestCase $test,
    )
    {
    }

    /**
     * @param MockObject $mock
     * @param array $functionsReturns
     * @return void
     */
    private function addFunctionsReturns(
        MockObject $mock,
        array $functionsReturns,
    ): void
    {
        foreach ($functionsReturns ?? [] as $functionName => $functionReturns){
            $mock->method($functionName)->willReturn($functionReturns);
        }
    }

    /**
     * @param array|null $functionsReturns
     * @return MockObject|ParameterDefinition
     */
    public function createParameterDefinition(
        ?array $functionsReturns=null,
    ): MockObject|ParameterDefinition
    {
        $response = $this->test->getMockBuilder(ParameterDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($functionsReturns !== null){
            $this->addFunctionsReturns(
                mock: $response,
                functionsReturns: $functionsReturns,
            );
        }

        return $response;
    }

    /**
     * @param array|null $functionsReturns
     * @return MockObject|MinimalismFactories
     */
    public function createMinimalismFactories(
        ?array $functionsReturns=null,
    ): MockObject|MinimalismFactories
    {
        $response = $this->test->getMockBuilder(MinimalismFactories::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($functionsReturns !== null){
            $this->addFunctionsReturns(
                mock: $response,
                functionsReturns: $functionsReturns,
            );
        }

        return $response;
    }

    /**
     * @param array|null $functionsReturns
     * @return MockObject|ServiceFactory
     */
    public function createServiceFactory(
        ?array $functionsReturns=null,
    ): MockObject|ServiceFactory
    {
        $response = $this->test->getMockBuilder(ServiceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($functionsReturns !== null){
            $this->addFunctionsReturns(
                mock: $response,
                functionsReturns: $functionsReturns,
            );
        }

        return $response;
    }

    /**
     * @param array|null $functionsReturns
     * @return MockObject|ObjectFactory
     */
    public function createObjectFactory(
        ?array $functionsReturns=null,
    ): MockObject|ObjectFactory
    {
        $response = $this->test->getMockBuilder(ObjectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($functionsReturns !== null){
            $this->addFunctionsReturns(
                mock: $response,
                functionsReturns: $functionsReturns,
            );
        }

        return $response;
    }

    /**
     * @param array|null $functionsReturns
     * @return MockObject|ModelParameters
     */
    public function createModelParameters(
        ?array $functionsReturns=null,
    ): MockObject|ModelParameters
    {
        $response = $this->test->getMockBuilder(ModelParameters::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($functionsReturns !== null){
            $this->addFunctionsReturns(
                mock: $response,
                functionsReturns: $functionsReturns,
            );
        }

        return $response;
    }

    /**
     * @return MockObject|ServiceInterface
     */
    public function createServiceInterface(
    ): MockObject|ServiceInterface
    {
        return $this->test->getMockBuilder(ServiceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject|ObjectInterface
     */
    public function createObjectInterface(
    ): MockObject|ObjectInterface
    {
        return $this->test->getMockBuilder(ObjectInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject|SimpleObjectInterface
     */
    public function createSimpleObjectInterface(
    ): MockObject|SimpleObjectInterface
    {
        return $this->test->getMockBuilder(SimpleObjectInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}