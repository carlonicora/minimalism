<?php
namespace CarloNicora\Minimalism\Tests\Unit\Core\Modules\Abstracts\Models;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractModel;
use CarloNicora\Minimalism\Tests\Unit\Abstracts\AbstractTestCase;
use CarloNicora\Minimalism\Tests\Unit\Mocks\Models\GenericModel;
use DateTime;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;

class AbstractModelTest extends AbstractTestCase
{
    /** @var MockObject|AbstractModel */
    private ?MockObject $abstractModel=null;

    /** @var GenericModel|null  */
    private ?GenericModel $genericModel=null;

    public function setUp(): void
    {
        parent::setUp();

        $this->genericModel = new GenericModel($this->services);

        $this->abstractModel = $this->getMockForAbstractClass(AbstractModel::class, [$this->services]);
    }

    public function testRedirect() : void
    {
        $this->assertEquals(null, $this->abstractModel->redirect());
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersEncrypted() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'stringParameter' => 'stringValue',
        ];

        $this->genericModel->initialise($passedParameters);

        $this->assertEquals(1, $this->genericModel->requiredEncryptedParameter);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersString() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'stringParameter' => 'stringValue',
        ];

        $this->genericModel->initialise($passedParameters);

        $this->assertEquals('stringValue', $this->genericModel->stringParameter);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersBool() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'boolParameter' => 'true',
        ];

        $this->genericModel->initialise($passedParameters);

        $this->assertEquals(true, $this->genericModel->boolParameter);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersDateTime1() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'dateTimeParameter' => '2020-01-01 00:00:00',
        ];

        $a = new DateTime('2020-01-01 00:00:00');

        $this->genericModel->initialise($passedParameters);

        $this->assertEquals($a, $this->genericModel->dateTimeParameter);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersDateTime2() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'dateTimeParameter2' => '1234567890',
        ];

        $a = new DateTime('@1234567890');

        $this->genericModel->initialise($passedParameters);

        $this->assertEquals($a, $this->genericModel->dateTimeParameter2);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersDateTimeFail() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'dateTimeParameter2' => 'ABC',
        ];

        $this->expectExceptionCode(412);

        $this->genericModel->initialise($passedParameters);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersInt() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'intParameter' => '123',
        ];

        $this->genericModel->initialise($passedParameters);

        $this->assertEquals(123, $this->genericModel->intParameter);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersFloat() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'floatParameter' => '123.124421',
        ];

        $this->genericModel->initialise($passedParameters);

        $this->assertEquals(123.124421, $this->genericModel->floatParameter);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersTimestamp1() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'timestampParameter1' => '1234567890',
        ];

        $this->genericModel->initialise($passedParameters);

        $this->assertEquals(1234567890, $this->genericModel->timestampParameter1);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersTimestamp2() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'timestampParameter2' => '2020-01-01 00:00:00',
        ];

        $a = new DateTime('2020-01-01 00:00:00');
        $b = $a->getTimestamp();

        $this->genericModel->initialise($passedParameters);

        $this->assertEquals($b, $this->genericModel->timestampParameter2);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersTimestampFails() : void
    {
        $passedParameters = [
            'requiredEncryptedParameter' => 1,
            'timestampParameter2' => 'ABC',
        ];

        $this->expectExceptionCode(412);

        $this->genericModel->initialise($passedParameters);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersValidatorNotFound() : void
    {
        $params = [
            'name' => ['validator' => 'NonExistingValidator']
        ];
        $this->setProperty($this->genericModel, 'parameters', $params);

        $this->expectExceptionCode(500);

        $this->genericModel->initialise([]);
    }

    /**
     * @throws Exception
     */
    public function testBuildParametersFailsRequiredNotPresent() : void
    {
        $this->expectExceptionCode(412);
        $this->genericModel->initialise([]);
    }
}