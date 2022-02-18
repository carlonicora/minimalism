<?php
/** @noinspection PhpIncludeInspection */
require_once '../../../../vendor/autoload.php';

use CarloNicora\Minimalism\Minimalism;
use CarloNicora\Minimiser\Models\Minimiser;

/**
 * @param string $dir
 * @param bool $recursive
 */
function deleteAllFilesInFolder(
    string $dir,
    bool $recursive=true,
): void
{
    foreach(glob($dir . '/*', GLOB_NOSORT) as $file) {
        if(is_file($file)) {
            unlink($file);
        } elseif ($recursive){
            deleteAllFilesInFolder($file);
            rmdir($file);
        }
    }
}

deleteAllFilesInFolder(__DIR__ . '/../../../../cache');

$minimalism = new Minimalism();
$minimalism->render(Minimiser::class);