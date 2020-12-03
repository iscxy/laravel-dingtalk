<?php

namespace Iscxy\Dingtalk;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;


class Dingtalk
{
    
    /**
     * 获取AccessToken
     */
    public function getAccessToken($appkey = '',$appsecret='')
    {
        if ( empty($appkey) || empty($appsecret) ) {
            return json_encode([
                'errCode' => 210001,
                'errMsg' => '缺少AppKey或AppSecret',
                ]);
        }else {
            if ( Cache::has('Dingtalk_AccessToken_'.$appkey) ) {
                //缺少进一步数据处理
                return Cache::get('Dingtalk_AccessToken_'.$appkey);
            } else {
                $response = Http::get('https://oapi.dingtalk.com/gettoken?appkey='. $appkey .'&appsecret='. $appsecret);
                if ($response->successful()) {

                    return $response->json();
                    
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




    /**
     * 刷新单个AccessToken
     */
    public function refreshAccessToken($appkey = '')
    {
        //获取 配置中 的 appkey 储存方式
        $configType = Config::get('dingtalk.type', 'config');
        switch ($configType) {
            case 'config':
                if ( empty($appkey) ) {
                    return json_encode(['errCode' => 210001,'errMsg' => '缺少AppKey或AppSecret',]);
                }else {
                    $appkeyList = Config::get('dingtalk.config');
                    if ( array_key_exists($appkey, $appkeyList) ) {
                        $rs = Http::get('https://oapi.dingtalk.com/gettoken?appkey='. $appkey .'&appsecret='. $appkeyList->$appkey);
                    } else {
                        return json_encode(['errCode' => 210002,'errMsg' => '无该AppKey项配置',]);
                    }
                }
                break;
            
            default:
                # code...
                break;
        }
    }

    /**
     * 刷新所有AccessToken
     */
    public function refreshAllAccessToken()
    {
        //获取 配置中 的 appkey 储存方式
        $configType = Config::get('dingtalk.type', 'config');
        switch ($configType) {
            case 'config':
                # code...
                break;
            
            default:
                # code...
                break;
        }



/*
        if ( $configType == 'config' ) {
            $appkeyList = Config::get('dingtalk.config');
            if ( !empty($appkey) ) {
                # $appkey == NULL
                foreach ( $appkeyList as $appkey => $appsecret ) {
                    $rs = Http::get('https://oapi.dingtalk.com/gettoken?appkey='. $appkey .'&appsecret='. $appsecret);
                    if ( $rs->successful() ) {
                        $rsl = $rs->json();
                        if ( array_key_exists('errcode', $rsl) ) {
                            if ( $rsl->errcode == 0 ) {
                                $accesstoken = json_encode([
                                    'errCode' => $rsl->errcode,
                                    'errMsg' => $rsl->errmsg,
                                    'access_token' => $rsl->access_token,
                                    'expires_in' => time()-200 + int($rsl->expires_in),
                                    ]);
                                if (Cache::forever('Dingtalk_AccessToken_'.$appkey, $accesstoken)) {
                                    return $accesstoken;
                                } else {
                                    return json_encode(['errCode' => 200002,'errMsg' => '将AccessAoken写入Cache缓存失败']);
                                }
                            } else {
                                # 钉钉全局错误代码
                                return json_encode(['errCode' => 'dtcode_'.$rsl->errcode,'errMsg' => $rsl->errmsg,]);
                            }
                        } else {
                            return json_encode(['errCode' => 210002,'errMsg' => '返回数据中缺少errcode键名',]);
                        }
                    } else {
                        return json_encode(['errCode' => 200001,'errMsg' => 'Http请求错误',]);
                    }
                }
            } else {
                # $appkey != NULL
            }
        } else {
            # $configType != 'config'
        }
        
*/












        
    }

























}
