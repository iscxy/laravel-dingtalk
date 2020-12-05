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
            'timeout'  => 5.0,
            'verify' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * 获取AccessToken
     * @param   string  $appkey 钉钉应用的唯一标识key。
     * @return  object  { "errCode": 0, "errMsg": "ok", "accesstoken": "4d54e90d66793c15ac4ca7e91a29904b", "expires": 1607157220 }
     */
    public function getAccessToken($appkey = '')
    {
        if ( empty($appkey) ) {
            return json_encode(['errCode' => 210001,'errMsg' => '缺少AppKey',]);
        } else {
            if (Cache::has('Dingtalk_AccessToken_'.$appkey)) {
                $rs = json_decode(Cache::get('Dingtalk_AccessToken_'.$appkey));
                if ( $rs->expires > time() ) {
                    return $rs;
                } else {
                    return $this->getRefreshAccessToken($appkey);
                }
            } else {
                return $this->getRefreshAccessToken($appkey);
            }
        }
    }

    /**
     * 刷新单个AccessToken
     * @param   string  $appkey 钉钉应用的唯一标识key。
     * @return  object  { "errCode": 0, "errMsg": "ok", "accesstoken": "4d54e90d66793c15ac4ca7e91a29904b", "expires": 1607157220 }
     */
    public function getRefreshAccessToken($appkey = '')
    {
        $appsecret = '';
        //获取 配置中 的 appkey 储存方式
        $configType = Config::get('dingtalk.type', 'config');
        switch ($configType) {
            case 'config':
                if ( empty($appkey) ) {
                    return json_encode(['errCode' => 210001,'errMsg' => '缺少AppKey或AppSecret',]);
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
                return json_encode(['errCode' => 210102,'errMsg' => '未知的 appkey 配置方式',]);
                break;
        }
        $this->refreshAccessToken($appkey,$appsecret);
    }

    /**
     * 刷新所有AccessToken
     * @return  object  { "errCode": 0, "errMsg": "ok", "accesstoken": "4d54e90d66793c15ac4ca7e91a29904b", "expires": 1607157220 }
     */
    public function refreshAllAccessToken()
    {

    }

    /**
     * 刷新单个AccessToken
     * @param   string  $appkey     应用的唯一标识。
     * @param   string  $appsecret  应用的密钥。
     * @return  object  { "errCode": 0, "errMsg": "ok", "accesstoken": "4d54e90d66793c15ac4ca7e91a29904b", "expires": 1607157220 }
     */
    protected function refreshAccessToken($appkey,$appsecret)
    {
        if ( empty($appkey) || empty($appsecret) ) {
            return json_encode(['errCode' => 210001,'errMsg' => '缺少AppKey或AppSecret',]);
        } else {
            try {
                $rs = $this->httpClient->get('https://oapi.dingtalk.com/gettoken?appkey='. $appkey .'&appsecret='. $appsecret);
                $getAT = json_decode($rs->getBody()->getContents(),true);
                /* $getAT =json_decode( (string) "{"errcode":0,"access_token":"4d54e90d66793c15ac4ca7e91a29904b","errmsg":"ok","expires_in":7200}"  ,true) */
                if (  array_key_exists('errcode', $getAT) ) {
                    if ( $getAT['errcode'] == 0 ) {
                        $accesstoken = json_encode([
                            'errCode' => $getAT['errcode'],
                            'errMsg' => $getAT['errmsg'],
                            'accesstoken' => $getAT['access_token'],
                            'expires' => time() -10 + (int)$getAT['expires_in'],
                        ]);
                        if ( Cache::forever('Dingtalk_AccessToken_'.$appkey, $accesstoken) ) {
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
            } catch (ConnectException $e) {
                return json_encode(['errCode' => 200001,'errMsg' => 'Http请求错误:-> '.substr($e->getMessage(),0,strpos($e->getMessage()," (")),]);
            }
        }
    }






}
