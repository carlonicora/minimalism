<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Commands;

use CarloNicora\Minimalism\Interfaces\EncrypterInterface;
use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\DecrypterInterface;

class DecrypterCommand implements DecrypterInterface
{
    /** @var EncrypterInterface|null */
    private ?EncrypterInterface $encrypter;

    /**
     * Decrypter constructor.
     * @param EncrypterInterface|null $encrypter=null
     */
    public function __construct(?EncrypterInterface $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    /**
     * @param string $parameter
     * @return int
     */
    public function decryptParameter(string $parameter): int
    {
        if ($this->encrypter === null){
            return (int)$parameter;
        }

        return $this->encrypter->decryptId($parameter);
    }
}