<?php
namespace CarloNicora\Minimalism\Services\Paths\Configurations;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceConfigurations;

class PathsConfigurations extends AbstractServiceConfigurations {
    /** @var array  */
    public array $logFolders=[];

    /**
     * mailingConfigurations constructor.
     */
    public function __construct() {
        $this->logFolders[] = realpath('.') . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR .
            'logs' . DIRECTORY_SEPARATOR . 'minimalism';

        foreach ($this->logFolders as &$logFolder) {
            if (substr($logFolder, strlen($logFolder)-1) !== DIRECTORY_SEPARATOR) {
                $logFolder .= DIRECTORY_SEPARATOR;
                $logFolder .= DIRECTORY_SEPARATOR;
            }
        }
    }
}