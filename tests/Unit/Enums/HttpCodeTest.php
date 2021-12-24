<?php
namespace CarloNicora\Minimalism\Tests\Unit\Enums;

use CarloNicora\Minimalism\Enums\HttpCode;
use PHPUnit\Framework\TestCase;

class HttpCodeTest extends TestCase
{
    /**
     * @return void
     */
    public function test100(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 100 Continue',
            actual: HttpCode::Continue->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test101(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 101 Switching Protocols',
            actual: HttpCode::SwitchingProtocols->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test102(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 102 Processing',
            actual: HttpCode::Processing->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test200(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 200 Ok',
            actual: HttpCode::Ok->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test201(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 201 Created',
            actual: HttpCode::Created->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test202(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 202 Accepted',
            actual: HttpCode::Accepted->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test203(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 203 Non-Authoritative Information',
            actual: HttpCode::NonAuthoritativeInformation->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test204(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 204 No Content',
            actual: HttpCode::NoContent->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test205(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 205 Reset Content',
            actual: HttpCode::ResetContent->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test206(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 206 Partial Content',
            actual: HttpCode::PartialContent->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test207(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 207 Multi-status',
            actual: HttpCode::MultiStatus->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test208(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 208 Already Reported',
            actual: HttpCode::AlreadyReported->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test300(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 300 Multiple Choices',
            actual: HttpCode::MultipleChoice->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test301(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 301 Moved Permanently',
            actual: HttpCode::MovedPermanently->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test302(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 302 Found',
            actual: HttpCode::Found->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test303(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 303 See Other',
            actual: HttpCode::SeeOther->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test304(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 304 Not Modified',
            actual: HttpCode::NotModified->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test305(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 305 Use Proxy',
            actual: HttpCode::UseProxy->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test306(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 306 Switch Proxy',
            actual: HttpCode::SwitchProxy->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test307(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 307 Temporary Redirect',
            actual: HttpCode::TemporaryRedirect->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test400(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 400 Bad Request',
            actual: HttpCode::BadRequest->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test401(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 401 Unauthorized',
            actual: HttpCode::Unauthorized->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test402(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 402 Payment Required',
            actual: HttpCode::PaymentRequired->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test403(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 403 Forbidden',
            actual: HttpCode::Forbidden->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test404(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 404 Not Found',
            actual: HttpCode::NotFound->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test405(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 405 Method Not Allowed',
            actual: HttpCode::MethodNotAllowed->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test406(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 406 Not Acceptable',
            actual: HttpCode::NotAcceptable->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test407(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 407 Proxy Authentication Required',
            actual: HttpCode::ProxyAuthenticationRequired->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test408(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 408 Request Time-out',
            actual: HttpCode::RequestTimeOut->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test409(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 409 Conflict',
            actual: HttpCode::Conflict->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test410(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 410 Gone',
            actual: HttpCode::Gone->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test411(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 411 Length Required',
            actual: HttpCode::LengthRequired->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test412(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 412 Precondition Failed',
            actual: HttpCode::PreconditionFailed->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test413(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 413 Request Entity Too Large',
            actual: HttpCode::RequestEntityTooLarge->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test414(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 414 Request-URI Too Large',
            actual: HttpCode::RequestURITooLarge->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test415(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 415 Unsupported Media Type',
            actual: HttpCode::UnsupportedMediaType->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test416(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 416 Requested range not satisfiable',
            actual: HttpCode::RequestedRangeNotSatisfiable->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test417(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 417 Expectation Failed',
            actual: HttpCode::ExpectationFailed->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test418(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 418 I\'m a teapot',
            actual: HttpCode::ImATeapot->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test422(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 422 Unprocessable Entity',
            actual: HttpCode::UnprocessableEntity->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test423(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 423 Locked',
            actual: HttpCode::Locked->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test424(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 424 Failed Dependency',
            actual: HttpCode::FailedDependency->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test425(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 425 Unordered Collection',
            actual: HttpCode::UnorderedCollection->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test426(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 426 Upgrade Required',
            actual: HttpCode::UpgradeRequired->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test428(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 428 Precondition Required',
            actual: HttpCode::PreconditionRequired->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test429(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 429 Too Many Requests',
            actual: HttpCode::TooManyRequests->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test431(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 431 Request Header Fields Too Large',
            actual: HttpCode::RequestHeaderFieldsTooLarge->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test451(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 451 Unavailable For Legal Reasons',
            actual: HttpCode::UnavailableForLegalReasons->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test500(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 500 Internal Server Error',
            actual: HttpCode::InternalServerError->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test501(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 501 Not Implemented',
            actual: HttpCode::NotImplemented->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test502(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 502 Bad Gateway',
            actual: HttpCode::BadGateway->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test503(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 503 Service Unavailable',
            actual: HttpCode::ServiceUnavailable->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test504(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 504 Gateway Time-out',
            actual: HttpCode::GatewayTimeOut->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test505(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 505 HTTP Version not supported',
            actual: HttpCode::HTTPVersionNotSupported->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test506(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 506 Variant Also Negotiates',
            actual: HttpCode::VariantAlsoNegotiates->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test507(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 507 Insufficient Storage',
            actual: HttpCode::InsufficientStorage->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test508(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 508 Loop Detected',
            actual: HttpCode::LoopDetected->getHttpResponseHeader(),
        );
    }

    /**
     * @return void
     */
    public function test511(
    ): void
    {
        self::assertEquals(
            expected: 'HTTP/1.1 511 Network Authentication Required',
            actual: HttpCode::NetworkAuthenticationRequired->getHttpResponseHeader(),
        );
    }
}