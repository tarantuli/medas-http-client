<?php

declare(strict_types=1);

namespace Medas\HttpClient;

class Curl
{
    const TRANSFER_INFO_SHAPE = [
        'url' => 'string',
        'content_type' => 'string',
        'http_code' => 'int',
        'header_size' => 'int',
        'request_size' => 'int',
        'filetime' => 'int',
        'ssl_verify_result' => 'int',
        'redirect_count' => 'int',
        'total_time' => 'float',
        'namelookup_time' => 'float',
        'connect_time' => 'float',
        'pretransfer_time' => 'float',
        'size_upload' => 'int',
        'size_download' => 'int',
        'speed_download' => 'int',
        'speed_upload' => 'int',
        'download_content_length' => 'int',
        'upload_content_length' => 'int',
        'starttransfer_time' => 'float',
        'redirect_time' => 'float',
        'certinfo' => 'array',
        'primary_ip' => 'int',
        'primary_port' => 'int',
        'local_ip' => 'string',
        'local_port' => 'int',
        'redirect_url' => 'int',
        'request_header' => 'int',
    ];
}
