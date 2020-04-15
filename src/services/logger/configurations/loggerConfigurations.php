<?php
namespace carlonicora\minimalism\services\logger\configurations;

use carlonicora\minimalism\core\services\abstracts\abstractServiceConfigurations;
use carlonicora\minimalism\services\paths\paths;

class loggerConfigurations extends abstractServiceConfigurations {
    /** @var string  */
    public string $errorLog;

    /** @var string  */
    public string $notificationLog;

    /** @var array|string[]  */
    protected array $dependencies = [
        paths::class
    ];

    /**
     * logger constructor.
     * @param string $rootPath
     */
    public function __construct(string $rootPath) {
        $directory = $rootPath .
            'logs' .
            DIRECTORY_SEPARATOR .
            'minimalism' .
            DIRECTORY_SEPARATOR;
        $this->errorLog = $directory . date('Ymd').'error.log';
        $this->notificationLog = $directory . date('Ymd').'notifications.log';
    }
}