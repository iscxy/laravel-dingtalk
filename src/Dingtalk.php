<?php

namespace Iscxy\Dingtalk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class Dingtalk
{

    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout'  => 2.0,
        ]);
    }

    /**
     * 刷新单个AccessToken
     */
    public function refreshAccessToken($appkey = '')
    {
        $appsecret = '';
        //获取 配置中 的 appkey 储存方式
        $configType = Config::get('dingtalk.type', 'config');
        switch ($configType) {
            case 'config':
                if ( empty($appkey) ) {
                    return json_encode(['errCode' => 210001,'errMsg' => '缺少AppKey',]);
                } else {
                    $appkeyList = Config::get('dingtalk.config');
                    if ( array_key_exists($appkey, $appkeyList) ) {
                        $appsecret = $appkeyList["$appkey"];
                    } else {
                        return json_encode(['errCode' => 210002,'errMsg' => '无该AppKey项对应配置信息',]);
                    }
                }
                break;
            case 'database':
                break;
            default:
                return json_encode(['errCode' => 210002,'errMsg' => '无该AppKey项对应配置信息',]);
                break;
        }
        try {
            $rs = $this->httpClient->get('https://oapi.dingtalk.com/gettoken?appkey='. $appkey .'&appsecret='. $appsecret);
            $getAT = $rs->getBody()->getContents();
            /** $getAT =  "{"errcode":0,"access_token":"4d54e90d66793c15ac4ca7e91a29904b","errmsg":"ok","expires_in":7200}" */
            if (gettype($getAT) == 'array') {
                if (  array_key_exists('errcode', $getAT) ) {
                    if ( $getAT['errcode'] == 0 ) {
                        $accesstoken = json_encode([
                            'errCode' => $getAT['errcode'],
                            'errMsg' => $getAT['errmsg'],
                            'access_token' => $getAT['access_token'],
                            'expires_in' => time() + int($getAT['expires_in']),
                            ]);
                            if (Cache::forever('Dingtalk_AccessToken_'.$appkey, $accesstoken)) {
                                return $accesstoken;
                            } else {
                                return json_encode(['errCode' => 200002,'errMsg' => '将AccessAoken写入Cache缓存失败']);
                            }
                    } else {
                        return json_encode(['errCode' => 210004,'errMsg' => '钉钉全局错误：[ '.$getAT['errcode'].' ]'.$getAT['errmsg'],]);
                    }
                } else {
                    return json_encode(['errCode' => 210003,'errMsg' => '返回数据中缺少errcode键名',]);
                }
            } else {
                dd($getAT);
            }
        } catch (ConnectException $e) {
            return json_encode(['errCode' => 200001,'errMsg' => 'Http请求错误',]);
        }
    }





}
