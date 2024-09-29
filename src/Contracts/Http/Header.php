<?php
declare(strict_types=1);

namespace Dropelikeit\ResponseFactory\Contracts\Http;

interface Header
{
    public const string HEADER_CONTENT_TYPE = 'Content-Type';
    public const string HEADER_CONTENT_ENCODING = 'Content-Encoding';
    public const string HEADER_CONTENT_ENCODING_BINARY = 'binary';
    public const string HEADER_CONTENT_LANGUAGE = 'Content-Language';
    public const string HEADER_CONTENT_LOCATION = 'Content-Location';
    public const string HEADER_ACCEPT = 'Accept';
    public const string HEADER_CONTENT_DISPOSITION = 'Content-Disposition';
    public const string HEADER_CONTENT_DISPOSITION_ATTACHMENT = 'attachment';
    public const string HEADER_CONTENT_DISPOSITION_FILENAME = 'filename';
    public const string HEADER_CONTENT_LENGTH = 'Content-Length';

    public const string HEADER_CONTENT_JSON = 'application/json';
    public const string HEADER_CONTENT_XML = 'application/xml';
    public const string HEADER_CONTENT_HTML = 'text/html';
    public const string HEADER_CONTENT_CSV = 'text/csv';
    public const string HEADER_CONTENT_TSV = 'text/vnd.ms-stream';
    public const string HEADER_CONTENT_GZIP = 'application/gzip';
}
