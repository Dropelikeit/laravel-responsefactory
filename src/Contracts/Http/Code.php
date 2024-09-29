<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Http;

/**
 * @author Marcel Strahl <info@marcel-strahl.de>
 */
interface Code
{
    public const int HTTP_CODE_CONTINUE = 100;
    public const int HTTP_CODE_SWITCHING_PROTOCOLS = 101;
    public const int HTTP_CODE_PROCESSING = 102;
    public const int HTTP_CODE_EARLY_HINTS = 103;
    public const int HTTP_CODE_OK = 200;
    public const int HTTP_CODE_CREATED = 201;
    public const int HTTP_CODE_ACCEPTED = 202;
    public const int HTTP_CODE_NON_AUTHORITATIVE_INFORMATION = 203;
    public const int HTTP_CODE_NO_CONTENT = 204;
    public const int HTTP_CODE_RESET_CONTENT = 205;
    public const int HTTP_CODE_PARTIAL_CONTENT = 206;
    public const int HTTP_CODE_MULTI_STATUS = 207;
    public const int HTTP_CODE_ALREADY_REPORTED = 208;
    public const int HTTP_CODE_IM_USED = 226;
    public const int HTTP_CODE_MULTIPLE_CHOICES = 300;
    public const int HTTP_CODE_MOVED_PERMANENTLY = 301;
    public const int HTTP_CODE_FOUND = 302;
    public const int HTTP_CODE_SEE_OTHER = 303;
    public const int HTTP_CODE_NOT_MODIFIED = 304;
    public const int HTTP_CODE_USE_PROXY = 305;
    public const int HTTP_CODE_RESERVED = 306;
    public const int HTTP_CODE_TEMPORARY_REDIRECT = 307;
    public const int HTTP_CODE_PERMANENTLY_REDIRECT = 308;
    public const int HTTP_CODE_BAD_REQUEST = 400;
    public const int HTTP_CODE_UNAUTHORIZED = 401;
    public const int HTTP_CODE_PAYMENT_REQUIRED = 402;
    public const int HTTP_CODE_FORBIDDEN = 403;
    public const int HTTP_CODE_NOT_FOUND = 404;
    public const int HTTP_CODE_METHOD_NOT_ALLOWED = 405;
    public const int HTTP_CODE_NOT_ACCEPTABLE = 406;
    public const int HTTP_CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const int HTTP_CODE_REQUEST_TIMEOUT = 408;
    public const int HTTP_CODE_CONFLICT = 409;
    public const int HTTP_CODE_GONE = 410;
    public const int HTTP_CODE_LENGTH_REQUIRED = 411;
    public const int HTTP_CODE_PRECONDITION_FAILED = 412;
    public const int HTTP_CODE_REQUEST_ENTITY_TOO_LARGE = 413;
    public const int HTTP_CODE_REQUEST_URI_TOO_LONG = 414;
    public const int HTTP_CODE_UNSUPPORTED_MEDIA_TYPE = 415;
    public const int HTTP_CODE_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const int HTTP_CODE_EXPECTATION_FAILED = 417;
    public const int HTTP_CODE_I_AM_A_TEAPOT = 418;
    public const int HTTP_CODE_MISDIRECTED_REQUEST = 421;
    public const int HTTP_CODE_UNPROCESSABLE_ENTITY = 422;
    public const int HTTP_CODE_LOCKED = 423;
    public const int HTTP_CODE_FAILED_DEPENDENCY = 424;
    public const int HTTP_CODE_TOO_EARLY = 425;
    public const int HTTP_CODE_UPGRADE_REQUIRED = 426;
    public const int HTTP_CODE_PRECONDITION_REQUIRED = 428;
    public const int HTTP_CODE_TOO_MANY_REQUESTS = 429;
    public const int HTTP_CODE_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    public const int HTTP_CODE_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    public const int HTTP_CODE_INTERNAL_SERVER_ERROR = 500;
    public const int HTTP_CODE_NOT_IMPLEMENTED = 501;
    public const int HTTP_CODE_BAD_GATEWAY = 502;
    public const int HTTP_CODE_SERVICE_UNAVAILABLE = 503;
    public const int HTTP_CODE_GATEWAY_TIMEOUT = 504;
    public const int HTTP_CODE_VERSION_NOT_SUPPORTED = 505;
    public const int HTTP_CODE_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;
    public const int HTTP_CODE_INSUFFICIENT_STORAGE = 507;
    public const int HTTP_CODE_LOOP_DETECTED = 508;
    public const int HTTP_CODE_NOT_EXTENDED = 510;
    public const int HTTP_CODE_NETWORK_AUTHENTICATION_REQUIRED = 511;
}
