<?php

declare(strict_types=1);

namespace Medas\HttpClient;

class CurlTransferInfoShape
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
        'redirect_url' => 'string',
        'primary_ip' => 'string',
        'certinfo' => 'array',
        'primary_port' => 'int',
        'local_ip' => 'string',
        'local_port' => 'int',
        'request_header' => 'string',
        'http_version' => 'int',
        'protocol' => 'int',
        'ssl_verifyresult' => 'int',
        'scheme' => 'string',
        'appconnect_time_us' => 'int',
        'connect_time_us' => 'int',
        'namelookup_time_us' => 'int',
        'pretransfer_time_us' => 'int',
        'redirect_time_us' => 'int',
        'starttransfer_time_us' => 'int',
        'total_time_us' => 'int',
    ];
}
