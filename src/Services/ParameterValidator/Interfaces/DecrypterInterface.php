<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Interfaces;

use CarloNicora\Minimalism\Interfaces\EncrypterInterface;

interface DecrypterInterface
{
    /**
     * DecrypterInterface constructor.
     * @param EncrypterInterface|null $encrypter
     */
    public function __construct(?EncrypterInterface $encrypter);

    /**
     * @param string $parameter
     */
    public function decryptParameter(string $parameter);
}