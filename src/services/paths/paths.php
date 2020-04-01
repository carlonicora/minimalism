<?php
namespace carlonicora\minimalism\services\paths;

use carlonicora\minimalism\core\services\abstracts\abstractService;
use Exception;
use RuntimeException;

class paths extends abstractService {
    /** @var string */
    private string $root;

    /** @var string */
    private string $url;

    /** @var string */
    private string $log;

    /**
     * paths constructor.
     * @throws Exception
     */
    public function __construct() {
        $this->url = (((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || isset($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';

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
     * @throws Exception
     */
    private function initialiseDirectoryStructure(): void {
        $this->root = realpath('.');

        $this->log = $this->root . DIRECTORY_SEPARATOR . 'logs';

        if (!file_exists($this->log) && !mkdir($this->log) && !is_dir($this->log)) {
            throw new RuntimeException('Cannot create log directory', 500);
        }

        $this->log .= DIRECTORY_SEPARATOR . 'minimalism';

        if (!file_exists($this->log) && !mkdir($this->log) && !is_dir($this->log)) {
            throw new RuntimeException('Cannot create log directory', 500);
        }
    }
}