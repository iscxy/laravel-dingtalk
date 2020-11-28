<?php

namespace Iscxy\Dingtalk;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class Dingtalk
{
    
    public function getAccessToken($appkey = '',$appsecret='')
    {
        if ( empty($appkey) ) {
            return json_encode([
                'errCode' => 210001,
                'errMsg' => '缺少AppKey',
                ]);
        } elseif( empty($appsecret) ) {
            return json_encode([
                'errCode' => 210002,
                'errMsg' => '缺少AppSecret',
                ]);
        }else {
            if ( Cache::has('Dingtalk_AccessToken_'.$appkey) ) {
                return Cache::get('Dingtalk_AccessToken_'.$appkey);
            } else {
                $response = Http::get('https://oapi.dingtalk.com/gettoken?appkey='. $appkey .'&appsecret='. $appsecret);
                if ($response->successful()) {

                    return  $response->json();
                    
                } else {
                    return json_encode([
                        'errCode' => 200001,
                        'errMsg' => 'Http请求错误',
                        ]);
                }
                
                /*
                $accesstoken = json_encode([
                        'access_token'=>'fw8ef8we8f76e6f7s8dxxxx',
                        'errcode'=>'0',
                        'errmsg'=>'ok',
                        'expires_in'=>'7200'
                ]);
                Cache::forever('Dingtalk_AccessToken_'.$appkey, $accesstoken);
                return $accesstoken;

                */


            }
        }
        





        //         Cache::forever('Dingtalk_AccessToken_'.$appkey, $a);
        //         return $a;
        //     }

    }

}
