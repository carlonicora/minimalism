<?php
namespace carlonicora\minimalism\helpers;

class headers {
    /** @var array|null */
    private static ?array $headers=null;

    /**
     * @param string $headerName
     * @return string|null
     */
    public static function getHeader(string $headerName): ?string {
        if (self::$headers === null){
            self::$headers = getallheaders();
        }

        return self::$headers[$headerName] ?? null;
    }
}

/**
 *
 */
if (!function_exists('getallheaders'))  {
    function getallheaders()
    {
        if (!is_array($_SERVER)) {
            return array();
        }

        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}