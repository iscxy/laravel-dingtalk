<?php

namespace Iscxy\Dingtalk;

use Illuminate\Support\Facades\Cache;


// use Illuminate\Support\Facades\Http;


class Dingtalk
{
    
    public function getAccessToken($appkey = '')
    {
        if (empty($appkey)) {
            return response()->json([
                'errCode' => 210001,
                'errMsg' => '缺少AppKey',
                ]);
        } else {
            if (Cache::has('AccessToken_'.$appkey)) {
                return Cache::get('AccessToken_'.$appkey);
            } else {
                return "get new AccessToken";
            }
        }
    }

}
