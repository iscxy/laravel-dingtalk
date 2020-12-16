<?php

namespace Iscxy\Dingtalk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;


/**
 * 角色管理
 * 文档网址：https://ding-doc.dingtalk.com/document#/org-dev-guide/list-roles
 */
class DtRole
{
    protected $httpClient;
    protected $appkey;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout'  => 5.0,
            'verify' => false,
        ]);
        $this->appkey = 'dingnz73k5e0j2zp9lrz';
    }
}