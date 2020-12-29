<?php
namespace CarloNicora\Minimalism\Interfaces;

interface EncryptedParameterInterface extends ParameterInterface
{
    /**
     * @param EncrypterInterface $encrypter
     * @return mixed
     */
    public function setEncrypter(EncrypterInterface $encrypter): void;
}