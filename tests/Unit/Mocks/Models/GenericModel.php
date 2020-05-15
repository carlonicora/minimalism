<?php
namespace CarloNicora\Minimalism\Tests\Unit\Mocks\Models;

use CarloNicora\Minimalism\Core\Modules\Abstracts\Models\AbstractModel;
use CarloNicora\Minimalism\Core\Response;
use CarloNicora\Minimalism\Services\ParameterValidator\ParameterValidator;
use DateTime;

class GenericModel extends AbstractModel
{
    public ?int $requiredEncryptedParameter=null;
    public ?string $stringParameter=null;
    public ?bool $boolParameter=null;
    public ?DateTime $dateTimeParameter=null;
    public ?DateTime $dateTimeParameter2=null;
    public ?int $intParameter=null;
    public ?float $floatParameter=null;
    public ?int $timestampParameter1=null;
    public ?int $timestampParameter2=null;

    protected array $parameters = [
        'requiredEncryptedParameter' => ['name' => 'requiredEncryptedParameter', 'encrypted' => true, 'required' => true],
        'stringParameter' => ['name' => 'stringParameter', 'validator' => ParameterValidator::PARAMETER_TYPE_STRING],
        'boolParameter' => ['name' => 'boolParameter', 'validator' => ParameterValidator::PARAMETER_TYPE_BOOL],
        'dateTimeParameter' => ['name' => 'dateTimeParameter', 'validator' => ParameterValidator::PARAMETER_TYPE_DATETIME],
        'dateTimeParameter2' => ['name' => 'dateTimeParameter2', 'validator' => ParameterValidator::PARAMETER_TYPE_DATETIME],
        'intParameter' => ['name' => 'intParameter', 'validator' => ParameterValidator::PARAMETER_TYPE_INT],
        'floatParameter' => ['name' => 'floatParameter', 'validator' => ParameterValidator::PARAMETER_TYPE_FLOAT],
        'timestampParameter1' => ['name' => 'timestampParameter1', 'validator' => ParameterValidator::PARAMETER_TYPE_TIMESTAMP],
        'timestampParameter2' => ['validator' => ParameterValidator::PARAMETER_TYPE_TIMESTAMP],
    ];

    public function DELETE(): Response
    {
        return new Response();
    }

    public function GET(): Response
    {
        $response = new Response();
        $response->data = 'data';

        return $response;
    }

    public function PUT(): Response
    {
        return new Response();
    }

    public function POST(): Response
    {
        return new Response();
    }


    public function preRender() : void
    {
    }
}