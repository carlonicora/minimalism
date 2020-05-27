<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator;

use CarloNicora\Minimalism\Core\Events\MinimalismInfoEvents;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ModelInterface;
use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractService;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceConfigurationsInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Configurations\ParameterValidatorConfigurations;
use CarloNicora\Minimalism\Services\ParameterValidator\Factories\ParameterValidatorFactory;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\ParameterValidatorFactoryInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Objects\ParameterObject;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\BoolValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\DateTimeValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\FloatValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\IntValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\StringValidator;
use CarloNicora\Minimalism\Services\ParameterValidator\Validators\TimestampValidator;
use Exception;

class ParameterValidator extends AbstractService {
    public const PARAMETER_TYPE_INT = IntValidator::class;
    public const PARAMETER_TYPE_STRING = StringValidator::class;
    public const PARAMETER_TYPE_BOOL = BoolValidator::class;
    public const PARAMETER_TYPE_TIMESTAMP = TimestampValidator::class;
    public const PARAMETER_TYPE_DATETIME = DateTimeValidator::class;
    public const PARAMETER_TYPE_FLOAT = FloatValidator::class;

    /** @var ParameterValidatorConfigurations  */
    private ParameterValidatorConfigurations $configData;

    /** @var ParameterValidatorFactoryInterface */
    private ParameterValidatorFactoryInterface $factory;

    /**
     * abstractApiCaller constructor.
     * @param ServiceConfigurationsInterface $configData
     * @param ServicesFactory $services
     */
    public function __construct(ServiceConfigurationsInterface $configData, ServicesFactory $services) {
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->configData = $configData;

        $this->factory = new ParameterValidatorFactory();
    }

    /**
     * @param ModelInterface $model
     * @param array|null $passedParameters
     * @throws Exception
     */
    public function validate(ModelInterface $model, ?array $passedParameters) : void
    {
        foreach ($model->getParameters() ?? [] as $parameterIdentifier=>$parameter){
            $parameterObject = new ParameterObject($parameterIdentifier, $parameter);

            $parameterValidator = $this->factory->createParameterValidator($this->services, $parameterObject);
            $parameterValidator->renderParameter($model, $passedParameters);
        }

        $this->services->logger()->info()->log(MinimalismInfoEvents::PARAMETERS_VALIDATED());
    }
}