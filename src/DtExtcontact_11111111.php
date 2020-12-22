<?php

namespace Iscxy\Dingtalk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

/**
 * 外部联系人
 * 文档网址：https://ding-doc.dingtalk.com/document#/org-dev-guide/extcontact-create
 */
class DtExtcontact
{
    protected $httpClient;
    protected $accessToken;
    protected $apiurl;

    public function __construct($accesstoken)
    {
        $this->httpClient = new Client([
            'timeout'  => 5.0,
            'verify' => false,
        ]);
        $this->accessToken = $accesstoken;
        $this->apiurl = 'https://oapi.dingtalk.com/topapi/v2';
    }
}