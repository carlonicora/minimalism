<?php
namespace CarloNicora\Minimalism\Services;

use CarloNicora\Minimalism\Interfaces\ServiceInterface;
use JetBrains\PhpStorm\Pure;

class Path implements ServiceInterface
{
    /** @var string */
    private string $root;

    /** @var string|null */
    private ?string $url=null;

    /**
     * Path constructor
     */
    #[Pure] public function __construct()
    {
        $this->root = dirname(__DIR__, 5);

        if (PHP_SAPI !== 'cli') {
            $this->url = (((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || isset($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . (array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : '') . '/';
        }
    }

    /**
     *
     */
    public function initialise(): void {}

    /**
     *
     */
    public function destroy(): void {}

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
}