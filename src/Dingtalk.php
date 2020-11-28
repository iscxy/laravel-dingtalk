<?php

namespace Iscxy\Dingtalk;



// use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\Http;


class Dingtalk
{
    
    public function getAccessToken($appkey = '')
    {
        if (empty($appkey)) {
            return response()->json([
                'errCode' => 101101,
                // 'errMsg' => Lang::get('dingtalk_errcode.101101'),
                ]);
        } else {
            return "getAccessToken";
        }
    }

}
