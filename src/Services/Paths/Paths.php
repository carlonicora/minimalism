<?php
namespace CarloNicora\Minimalism\Services\Paths;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractService;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceConfigurationsInterface;
use CarloNicora\Minimalism\Services\Paths\configurations\PathsConfigurations;
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

    /** @var string */
    private string $log;

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


        $this->url = (((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || isset($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . (array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : '') . '/';

        $this->initialiseDirectoryStructure();
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
     * @return string
     */
    public function getLog() : string {
        return $this->log;
    }

    /**
     * @return string
     * @throws JsonException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public function getNamespace() : string {
        $content = file_get_contents( $this->root . DIRECTORY_SEPARATOR . 'composer.json');
        $content = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        return key($content['autoload']['psr-4']);
    }

    /**
     * @throws Exception
     */
    private function initialiseDirectoryStructure(): void {
        $this->root = realpath('.');

        $this->log = $this->root . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logs';

        if (!file_exists($this->log) && !mkdir($this->log) && !is_dir($this->log)) {
            throw new RuntimeException('Cannot create log directory', 500);
        }

        $this->log .= DIRECTORY_SEPARATOR . 'minimalism';

        if (!file_exists($this->log) && !mkdir($this->log) && !is_dir($this->log)) {
            throw new RuntimeException('Cannot create log directory', 500);
        }
    }

    /**
     * @return array
     */
    public function getLogFolders() : array {
        return $this->configData->logFolders;
    }
}