<?php
namespace CarloNicora\Minimalism\Objects;

class ModelParameters
{
    /** @var array  */
    private array $namedParameters=[];

    /** @var array  */
    private array $files=[];

    /** @var array  */
    private array $positionedParameters=[];

    /**
     *
     */
    public function __construct(
    )
    {
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function addNamedParameter(
        string $name,
        mixed $value,
    ): void
    {
        $this->namedParameters[$name] = $value;
    }

    /**
     * @param string $fileName
     * @param array $fileDefinition
     */
    public function addFile(
        string $fileName,
        array $fileDefinition,
    ): void
    {
        $this->files[$fileName] = $fileDefinition;
    }

    /**
     * @param string $fileName
     * @return array|null
     */
    public function getFile(
        string $fileName
    ): ?array
    {
        return $this->files[$fileName] ?? null;
    }

    /**
     * @return array
     */
    public function getFiles(
    ): array
    {
        return $this->files;
    }

    /**
     * @param mixed $value
     */
    public function addPositionedParameter(
        mixed $value,
    ): void
    {
        $this->positionedParameters[] = $value;
    }

    /**
     * @return mixed
     */
    public function getNextPositionedParameter(
    ): mixed
    {
        if ($this->positionedParameters === []){
            return null;
        }

        return array_shift($this->positionedParameters);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getNamedParameter(
        string $name,
    ): mixed
    {
        return $this->namedParameters[$name] ?? null;
    }
}