<?php
namespace CarloNicora\Minimalism\Services\Paths;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractService;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Exceptions\ServiceNotFoundException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceConfigurationsInterface;
use CarloNicora\Minimalism\Services\Paths\Configurations\PathsConfigurations;
use Exception;
use JsonException;
use RuntimeException;

class Paths extends AbstractService {
    /** @var PathsConfigurations  */
    private PathsConfigurations $configData;

    /** @var string */
    private string $root;

    /** @var string */
    private string $url;

    /**
     * abstractApiCaller constructor.
     * @param ServiceConfigurationsInterface $configData
     * @param ServicesFactory $services
     * @throws Exception
     */
    public function __construct(ServiceConfigurationsInterface $configData, ServicesFactory $services) {
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->configData = $configData;

        $this->root = realpath('.');

        $this->url = (((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || isset($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . (array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : '') . '/';
    }

    /**
     * @return string
     */
    public function getModelsFolder() : string
    {
        try {
            $content = file_get_contents($this->root . DIRECTORY_SEPARATOR . 'composer.json');
            $content = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException|Exception|ServiceNotFoundException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }

        try {
            $sourceFolder = current($content['autoload']['psr-4']);
        } catch (Exception $e) {
            throw new ConfigurationException('minimalism', 'namespace not found in composer', ConfigurationException::ERROR_NAMESPACE_NOT_CONFIGURED);
        }

        return $this->root
            . DIRECTORY_SEPARATOR . $sourceFolder . 'Models' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getRoot() : string {
        return $this->root;
    }

    /**
     * @return string
     */
    public function getUrl() : string {
        return $this->url;
    }

    /**
     * @param string $version
     */
    public function setUrlVersion(string $version) : void
    {
        if (substr($this->url, -(strlen($version) + 1)) !== $version . '/') {
            $this->url .= $version . '/';
        }
    }

    /**
     * @return string
     */
    public function getLog() : string {
        return $this->root
            . DIRECTORY_SEPARATOR . 'data'
            . DIRECTORY_SEPARATOR . 'logs'
            . DIRECTORY_SEPARATOR . 'minimalism'
            .DIRECTORY_SEPARATOR;
    }

    public function getCache() : string
    {
        return $this->root . DIRECTORY_SEPARATOR
            . 'data' . DIRECTORY_SEPARATOR
            . 'cache' . DIRECTORY_SEPARATOR
            . 'services.cache';
    }

    /**
     * @return string
     * @throws JsonException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function getNamespace() : string {
        try {
            $content = file_get_contents($this->root . DIRECTORY_SEPARATOR . 'composer.json');
            $content = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException|Exception|ServiceNotFoundException $e) {
            throw new RuntimeException($e->getMessage(), 500);
        }

        try {
            $namespace = key($content['autoload']['psr-4']);
        } catch (Exception $e) {
            throw new ConfigurationException('minimalism', 'namespace not found in composer', ConfigurationException::ERROR_NAMESPACE_NOT_CONFIGURED);
        }

        return $namespace;
    }

    /**
     * @throws Exception
     */
    public function initialiseDirectoryStructure(): void
    {
        $this->validateDirectory(($directory = $this->root . DIRECTORY_SEPARATOR . 'data'));
        $this->validateDirectory(($directory .= DIRECTORY_SEPARATOR . 'logs'));
        $this->validateDirectory($directory . DIRECTORY_SEPARATOR . 'minimalism');
    }

    /**
     * @param string $directory
     * @throws Exception
     */
    private function validateDirectory(string $directory) : void
    {
        try {
            if (!file_exists($directory) && !mkdir($directory, 0777) && !is_dir($directory)) {
                throw new RuntimeException('Cannot create log directory', 500);
            }
        } catch (Exception $e) {
            throw new RuntimeException('Cannot create log directory', 500);
        }
    }
}