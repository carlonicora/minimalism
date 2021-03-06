<?php
namespace CarloNicora\Minimalism\Services;

use CarloNicora\Minimalism\Interfaces\ServiceInterface;

class Path implements ServiceInterface
{
    /** @var string */
    private string $root;

    /** @var string|null */
    private ?string $url=null;

    /** @var array  */
    private array $servicesModels=[];

    /** @var array  */
    private array $servicesModelsDirectories = [];

    /** @var array  */
    private array $servicesViewsDirectories = [];

    /**
     * Path constructor
     */
    public function __construct()
    {
        $this->root = dirname(__DIR__, 5);

        $this->initialise();

        $this->loadServicesViewsAndModelsDirectories();
    }

    /**
     *
     */
    public function initialise(): void {
        if (PHP_SAPI === 'cli') {
            $this->url = null;
        } else {
            $this->url = (((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || isset($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . (array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : '') . '/';

            $uri = $_SERVER['REQUEST_URI'] ?? '';
            if (($versioning = $this->sanitiseUriVersion($uri)) !== ''){
                $this->url .= $versioning . '/';
            }
        }
    }

    /**
     *
     */
    public function destroy(): void {}

    /**
     * @param string $uri
     * @return string
     */
    public function sanitiseUriVersion(string &$uri): string
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
     *
     */
    private function loadServicesViewsAndModelsDirectories(): void
    {
        $plugins = glob($this->root . '/vendor/*/minimalism-service-*/src');
        $internal = glob($this->root . '/src/Services/*');

        $files = array_unique(array_merge($plugins, $internal));

        foreach ($files as $fileName) {
            $possibleModelDirectory = $fileName . DIRECTORY_SEPARATOR . 'Models';
            if (file_exists($possibleModelDirectory) && is_dir($possibleModelDirectory)){
                $this->servicesModelsDirectories[] = $possibleModelDirectory;
            }

            $possibleViewDirectory = $fileName . DIRECTORY_SEPARATOR . 'Views';
            if (file_exists($possibleViewDirectory) && is_dir($possibleViewDirectory)){
                $this->servicesViewsDirectories[] = $possibleViewDirectory;
            }
        }
    }
}