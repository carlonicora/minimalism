<?php
namespace carlonicora\minimalism\services\paths\configurations;

use carlonicora\minimalism\core\services\abstracts\abstractServiceConfigurations;

class pathsConfigurations extends abstractServiceConfigurations {
    /** @var array  */
    public array $logFolders=[];

    /**
     * mailingConfigurations constructor.
     */
    public function __construct() {
        $this->logFolders[] = realpath('.') . DIRECTORY_SEPARATOR .
            'logs' . DIRECTORY_SEPARATOR . 'minimalism';

        if((getenv('MINIMALISM_ADDITIONAL_LOG_FOLDERS'))){
            $this->logFolders = explode(',', getenv('MINIMALISM_ADDITIONAL_LOG_FOLDERS'));
        }

        foreach ($this->logFolders as &$logFolder) {
            if (substr($logFolder, strlen($logFolder)-1) !== DIRECTORY_SEPARATOR) {
                $logFolder .= DIRECTORY_SEPARATOR;
                $logFolder .= DIRECTORY_SEPARATOR;
            }
        }
    }
}