<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Objects;

use CarloNicora\Minimalism\Services\ParameterValidator\Interfaces\DecrypterInterface;

class DefaultDecrypter implements DecrypterInterface
{
    /**
     * @param string $parameter
     * @return int
     */
    public function decryptParameter(string $parameter) : int
    {
        return (int)$parameter;
    }

}