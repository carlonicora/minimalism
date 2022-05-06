<?php
namespace CarloNicora\Minimalism\Services;

use CarloNicora\Minimalism\Abstracts\AbstractService;

class Path extends AbstractService
{
    /** @var string */
    protected string $root;

    /** @var string|null */
    protected ?string $url=null;

    /** @var string|null  */
    protected ?string $uri=null;

    /** @var array  */
    private array $servicesModels=[];

    /** @var array  */
    private array $servicesModelsDirectories = [];

    /** @var array  */
    private array $servicesViewsDirectories = [];

    /**
     * Path constructor
     */
    public function __construct(
    )
    {
        $directory = __DIR__;
        if ($this->isServicesDirectory($directory)){
            $this->root = dirname(path: $directory, levels: 2);
        } else {
            $this->root = dirname(path: $directory, levels: 5);
        }

        $this->initialise();

        $this->loadServicesViewsAndModelsDirectories();
    }

    /**
     *
     */
    public function initialise(): void {
        if ($this->isCLIMode()) {
            $this->url = null;
        } else {
            $this->url = $this->getProtocol() . '://' . (array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : '') . '/';

            $this->uri = $_SERVER['REQUEST_URI'] ?? '';
            if (($versioning = $this->sanitiseUriVersion($this->uri)) !== ''){
                $this->url .= $versioning . '/';
            }
        }
    }

    /**
     * @param string $uri
     * @return string
     */
    public function sanitiseUriVersion(
        string &$uri,
    ): string
    {
        $response = '';
        $uriParts = explode('/', $uri);

        if (str_starts_with($uriParts[1], 'v') && is_numeric($uriParts[1][1])){
            $response = $uriParts[1];
            array_shift($uriParts);
            array_shift($uriParts);
            $uri = '/' . implode('/', $uriParts);
        }

        return $response;
    }

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

    /**
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @param string $cacheName
     * @return string
     */
    public function getCacheFile(
        string $cacheName
    ): string
    {
        return $this->getRoot() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $cacheName;
    }

    /**
     * @return array
     */
    public function getServicesModels(): array
    {
        return $this->servicesModels;
    }

    /**
     * @param array $servicesModels
     */
    public function setServicesModels(array $servicesModels): void
    {
        $this->servicesModels = $servicesModels;
    }

    /**
     * @return array
     */
    public function getServicesModelsDirectories(): array
    {
        return $this->servicesModelsDirectories;
    }

    /**
     * @param string $directory
     */
    public function addServiceModelDirectory(string $directory): void
    {
        $this->servicesModelsDirectories[] = $directory;
    }

    /**
     * @return array
     */
    public function getServicesViewsDirectories(): array
    {
        return $this->servicesViewsDirectories;
    }
    
    /**
     * @param string $directory
     */
    public function addServiceViewDirectory(string $directory): void
    {
        $this->servicesViewsDirectories[] = $directory;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || isset($_SERVER['HTTPS']))
            ? 'https'
            : 'http';
    }

    /**
     * @codeCoverageIgnore
     * @return bool
     */
    protected function isCLIMode(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * @param string $directory
     * @return bool
     */
    public function isServicesDirectory(string $directory): bool
    {
        return $directory === '/app/src/Services';
    }

    /**
     * @return void
     */
    protected function loadServicesViewsAndModelsDirectories(): void
    {
        $minimalisers = glob($this->root . '/vendor/*/minimalise*/src', GLOB_NOSORT);
        $plugins = glob($this->root . '/vendor/*/minimalism-service-*/src', GLOB_NOSORT);
        $internal = glob($this->root . '/src/Services/*', GLOB_NOSORT);

        foreach (array_unique(array_merge($minimalisers, $plugins, $internal)) as $fileName) {
            $possibleModelDirectory = $fileName . DIRECTORY_SEPARATOR . 'Models';
            if (is_dir($possibleModelDirectory)){
                $this->addServiceModelDirectory($possibleModelDirectory);
            }

            $possibleViewDirectory = $fileName . DIRECTORY_SEPARATOR . 'Views';
            if (is_dir($possibleViewDirectory)){
                $this->addServiceViewDirectory($possibleViewDirectory);
            }
        }
    }

    /**
     * @return string|null
     */
    public static function getBaseInterface(): ?string
    {
        return  null;
    }
}