<?php
namespace CarloNicora\Minimalism\Enums;

enum HttpCache
{
    case NoCache;
    case Cache;

    public function write(
        int $cacheDuration=3600,
    ): void {
        switch ($this) {
            case self::NoCache:
                header('Expires: Fri, 20 May 1977 14:00:00 GMT');
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Pragma: no-cache');
                break;
            case self::Cache:
                header('Cache-Control: max-age=' . $cacheDuration);
                header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cacheDuration) . ' GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
                header('Pragma: public');
                break;
        }
    }
}