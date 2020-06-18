<?php
namespace CarloNicora\Minimalism\Core\Traits;

trait HttpHeadersTrait
{
    /** @var array|null */
    protected ?array $headers = null;

    /**
     * @param string $headerName
     * @return string|null
     */
    public function getHeader(string $headerName): ?string
    {
        if ($this->headers === null) {
            $this->headers = getallheaders();
        }

        return $this->headers[$headerName] ?? null;
    }
}

// @codeCoverageIgnoreStart
if (!function_exists('getallheaders'))
{
    // @codeCoverageIgnoreEnd
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER ?? [] as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
    // @codeCoverageIgnoreStart
}
// @codeCoverageIgnoreEnd
