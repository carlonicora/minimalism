<?php
namespace carlonicora\minimalism\helpers;


class apiResponse {
    /** @var bool */
    public bool $isSuccess;

    /** @var array|null */
    public ?array $returnedValue;

    /** @var int */
    public int $errorId;

    /** @var string */
    public string $errorMessage;
}