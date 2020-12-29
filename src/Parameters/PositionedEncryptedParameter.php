<?php
namespace CarloNicora\Minimalism\Parameters;

use CarloNicora\Minimalism\Interfaces\EncryptedParameterInterface;
use CarloNicora\Minimalism\Interfaces\EncrypterInterface;

class PositionedEncryptedParameter extends PositionedParameter implements EncryptedParameterInterface
{
    /**
     * @var EncrypterInterface
     */
    private EncrypterInterface $encrypter;

    /**
     * @param EncrypterInterface $encrypter
     * @return mixed
     */
    public function setEncrypter(EncrypterInterface $encrypter): void
    {
        $this->encrypter = $encrypter;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->encrypter->decrypt($this->value);
    }
}