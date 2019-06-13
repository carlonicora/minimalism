<?php
namespace carlonicora\minimalism\helpers;

use carlonicora\minimalism\abstracts\abstractConfigurations;

class errorReporter {
    /**
     * @param abstractConfigurations $configurations
     * @param string $errorCode
     * @param string $errorMessage
     * @param string $httpCode
     */
    public static function report($configurations, $errorCode, $errorMessage=null, $httpCode=null){
        $errorLog = date('d.m.Y H:i:s') . ' | ' . $errorCode . ' | ' . self::returnErrorMessage($errorCode) . PHP_EOL;
        if (!empty($errorMessage)){
            $errorLog .= ' (' . $errorMessage . ')' . PHP_EOL;
        }

        $backTrace = debug_backtrace();
        while($caller = array_shift($backTrace)){
            $errorLog .= '    ' . $caller['file'] . '|' . $caller['line'] . PHP_EOL;
        }

        error_log($errorLog.PHP_EOL, 3, $configurations->getErrorLog());

        if (isset($httpCode)) self::returnHttpCode($httpCode);
    }

    private static function returnErrorMessage($errorCode){
        switch ($errorCode) {
            case 1: $returnValue = 'Cannot load configuration file'; break;
            case 2: $returnValue = 'Cannot load database configurations'; break;
            case 3: $returnValue = 'Endpoint not found'; break;
            case 4: $returnValue = 'Views not found'; break;
            case 5: $returnValue = 'Views not found'; break;
            case 6: $returnValue = 'Cannot load functions'; break;
            case 7: $returnValue = 'Error loading the database files'; break;
            case 8: $returnValue = 'Invalid Token'; break;
            case 9: $returnValue = 'Invalid Request Time'; break;
            case 10: $returnValue = 'Invalid Client'; break;
            case 11: $returnValue = 'Invalid Auth'; break;
            case 12: $returnValue = 'Inactive account'; break;
            case 13: $returnValue = 'Inactive password'; break;
            case 14: $returnValue = 'Invalid account'; break;
            case 15: $returnValue = 'Error generating new auth record in the database'; break;
            case 16: $returnValue = 'Error deleting the session'; break;
            case 17: $returnValue = 'Invalid Security Code'; break;
            case 18: $returnValue = 'Security Code expired'; break;
            case 19: $returnValue = 'Security Code and User mismatch'; break;
            case 20: $returnValue = 'Error returned from API call'; break;
            default: $returnValue = 'Generic Error'; break;
        }

        return($returnValue);
    }

    public static function returnHttpCode($code){
        switch ($code) {
            case 100: $text = 'Continue'; break;
            case 101: $text = 'Switching Protocols'; break;
            case 200: $text = 'OK'; break;
            case 201: $text = 'Created'; break;
            case 202: $text = 'Accepted'; break;
            case 203: $text = 'Non-Authoritative Information'; break;
            case 204: $text = 'No Content'; break;
            case 205: $text = 'Reset Content'; break;
            case 206: $text = 'Partial Content'; break;
            case 300: $text = 'Multiple Choices'; break;
            case 301: $text = 'Moved Permanently'; break;
            case 302: $text = 'Moved Temporarily'; break;
            case 303: $text = 'See Other'; break;
            case 304: $text = 'Not Modified'; break;
            case 305: $text = 'Use Proxy'; break;
            case 400: $text = 'Bad Request'; break;
            case 401: $text = 'Unauthorized'; break;
            case 402: $text = 'Payment Required'; break;
            case 403: $text = 'Forbidden'; break;
            case 404: $text = 'Not Found'; break;
            case 405: $text = 'Method Not Allowed'; break;
            case 406: $text = 'Not Acceptable'; break;
            case 407: $text = 'Proxy Authentication Required'; break;
            case 408: $text = 'Request Time-out'; break;
            case 409: $text = 'Conflict'; break;
            case 410: $text = 'Gone'; break;
            case 411: $text = 'Length Required'; break;
            case 412: $text = 'Precondition Failed'; break;
            case 413: $text = 'Request Entity Too Large'; break;
            case 414: $text = 'Request-URI Too Large'; break;
            case 415: $text = 'Unsupported Media Type'; break;
            case 422: $text = 'Unprocessable Entity'; break;
            case 500: $text = 'Internal Server Error'; break;
            case 501: $text = 'Not Implemented'; break;
            case 502: $text = 'Bad Gateway'; break;
            case 503: $text = 'Service Unavailable'; break;
            case 504: $text = 'Gateway Time-out'; break;
            case 505: $text = 'HTTP Version not supported'; break;
            default:
                exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
        }

        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1');

        header($protocol . ' ' . $code . ' ' . $text);

        $GLOBALS['http_response_code'] = $code;

        if ($code != 200){
            exit;
        }
    }
}