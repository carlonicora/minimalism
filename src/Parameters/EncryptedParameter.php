<?php

namespace CarloNicora\Minimalism\Parameters;

use CarloNicora\Minimalism\Interfaces\EncryptedParameterInterface;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;

class EncryptedParameter implements EncryptedParameterInterface
{
    /**
     * @var EncrypterInterface
     */
    private EncrypterInterface $encrypter;

    /**
     * @param EncrypterInterface $encrypter
     */
    public function setEncrypter(EncrypterInterface $encrypter): void
    {
        $this->encrypter = $encrypter;
    }

    /**
     * EnctyptedParameter constructor.
     * @param mixed $value
     */
    public function __construct(
        private mixed $value
    )
    {
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->encrypter->decryptId($this->value);
    }

    /**
     * @return mixed
     */
    public function getEncryptedValue(): mixed
    {
        return $this->value;
    }
}