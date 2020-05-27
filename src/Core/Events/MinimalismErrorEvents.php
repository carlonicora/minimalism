<?php
namespace CarloNicora\Minimalism\Core\Events;

use CarloNicora\Minimalism\Core\Events\Abstracts\AbstractErrorEvent;
use CarloNicora\Minimalism\Core\Events\Interfaces\EventInterface;
use CarloNicora\Minimalism\Core\Modules\Interfaces\ResponseInterface;
use Exception;

class MinimalismErrorEvents extends AbstractErrorEvent
{
    protected string $serviceName = 'minimalism';

    public static function GENERIC_ERROR(Exception $e=null) : EventInterface
    {
        return new self(
            1,
            ResponseInterface::HTTP_STATUS_500,
            'Exception Generated',
            [],
            $e
        );
    }

    public static function SERVICE_CACHE_ERROR(Exception $e) : EventInterface
    {
        return new self(
            2,
            ResponseInterface::HTTP_STATUS_500,
            'Services could not be cached',
            [],
            $e
        );
    }

    public static function COOKIE_SETTING_ERROR(Exception $e) : EventInterface
    {
        return new self(
            3,
            ResponseInterface::HTTP_STATUS_500,
            'Services could not be saved in the cookies',
            [],
            $e
        );
    }

    public static function CONFIGURATION_ERROR(string $missingConfiguration) : EventInterface
    {
        return new self(
            4,
            ResponseInterface::HTTP_STATUS_500,
            'Error in the environment configuration: %s',
            [$missingConfiguration]
        );
    }

    public static function PARAMETER_VALIDATOR_ERROR(string $parameterValidatorClass, Exception $e) : EventInterface
    {
        return new self(
            5,
            ResponseInterface::HTTP_STATUS_500,
            'Parameter validator class cannot be found: %s',
            [$parameterValidatorClass],
            $e
        );
    }

    public static function MODEL_NOT_FOUND(string $modelName) : EventInterface
    {
        return new self(
            6,
            ResponseInterface::HTTP_STATUS_404,
            'Model not found: %s',
            [$modelName]
        );
    }

    public static function MODULE_NOT_LOADED() : EventInterface
    {
        return new self(
            7,
            ResponseInterface::HTTP_STATUS_500,
            'No module configured'
        );
    }

    public static function MULTIPLE_MODULES_LOADED() : EventInterface
    {
        return new self(
            8,
            ResponseInterface::HTTP_STATUS_500,
            'Multiple modules configured'
        );
    }

    public static function SERVICE_NOT_FOUND(string $serviceName) : EventInterface
    {
        return new self(
            9,
            ResponseInterface::HTTP_STATUS_500,
            'Service not found: %s',
            [$serviceName]
        );
    }

    public static function REQUIRED_PARAMETER_MISSING(string $parameterName) : EventInterface
    {
        return new self(
            10,
            ResponseInterface::HTTP_STATUS_412,
            'Required parameter missing: %s',
            [$parameterName]
        );
    }

    public static function PARAMETER_TYPE_MISMATCH(string $parameterName) : EventInterface
    {
        return new self(
            11,
            ResponseInterface::HTTP_STATUS_412,
            'Parameter Type mismatch: %s',
            [$parameterName]
        );
    }

    public static function COMPOSER_FILE_MISCONFIGURED() : EventInterface
    {
        return new self(
            12,
            ResponseInterface::HTTP_STATUS_500,
            'Composer file misconfigured'
        );
    }

    public static function NAMESPACE_MISSING() : EventInterface
    {
        return new self(
            13,
            ResponseInterface::HTTP_STATUS_500,
            'Namespace missing in composer file'
        );
    }

    public static function MODELS_FOLDER_MISSING() : EventInterface
    {
        return new self(
            14,
            ResponseInterface::HTTP_STATUS_500,
            'The folder for the models is missing in the app'
        );
    }
 }