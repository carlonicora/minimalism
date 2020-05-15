<?php
namespace CarloNicora\Minimalism\Services\ParameterValidator\Interfaces;

interface DecrypterInterface
{
    /**
     * @param string $parameter
     */
    public function decryptParameter(string $parameter);
}