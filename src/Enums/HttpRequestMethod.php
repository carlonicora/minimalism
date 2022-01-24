<?php
namespace CarloNicora\Minimalism\Enums;

enum HttpRequestMethod: string
{
    case Get='GET';
    case Post='POST';
    case Put='PUT';
    case Delete='DELETE';
    case Patch='PATCH';

    /** PATCH TO USE MINIMALISM IN CLI MODE */
    case Cli='CLI';

    /**
     * Not yet supported
     *
     * case Head;
     * case Connect;
     * case Options;
     * case Trace
     */

    /**
     * @return HttpCode
     */
    public function getDefaultResponse(
    ): HttpCode
    {
        return match($this) {
            self::Get, self::Cli => HttpCode::Ok,
            self::Post => HttpCode::Created,
            self::Put, self::Patch, self::Delete => HttpCode::NoContent,
        };
    }
}