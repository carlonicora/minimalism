<?php
namespace carlonicora\minimalism\core\traits;

trait filesystem {
    /**
     * @param string $fileName
     * @return string
     */
    private function getClassNameFromFile(string $fileName) : string {
        $fp = fopen($fileName, 'rb');
        $class = $namespace = $buffer = '';
        $i = 0;
        while (!$class) {
            if (feof($fp)) {
                break;
            }

            $buffer .= fread($fp, 512);
            $tokens = token_get_all($buffer);

            if (strpos($buffer, '{') === false) {
                continue;
            }

            for ($iMax = count($tokens); $i< $iMax; $i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j=$i+1, $jMax = count($tokens); $j< $jMax; $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= '\\'.$tokens[$j][1];
                        } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }

                if ($tokens[$i][0] === T_CLASS) {
                    for ($j=$i+1, $jMax = count($tokens); $j< $jMax; $j++) {
                        if ($tokens[$j] === '{') {
                            $class = $tokens[$i+2][1];
                        }
                    }
                }
            }
        }

        return substr($namespace, 1) . '\\' . $class;
    }
}