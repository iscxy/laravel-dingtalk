<?php

namespace Iscxy\Dingtalk;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class Dingtalk
{
    protected $accesstoken;
    protected $appkey;
    protected $appsecret;

    public function __construct()
    {
        $this->getAccessToken();
    }

    public function getAccessToken($appkey = '')
    {
        if ($appkey == '') {
            return json_encode([]);
        } else {
            # code...
        }
        
        if (Cache::has('AppKey_'.$appkey)) {
            //
        } else {
            # code...
        }

        $url = "https://oapi.dingtalk.com/gettoken?appkey=". $this->appkey ."&appsecret=". $this->appsecret;



        return "";
    }

}
