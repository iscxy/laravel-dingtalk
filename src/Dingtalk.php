<?php

namespace Iscxy\Dingtalk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

// use Illuminate\Support\Facades\DB;

class Dingtalk
{

    protected $httpClient;
    protected $Monolog;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout'  => 10.0,
            'verify' => false,
        ]);
        if ( array_key_exists('logfilename', Config::get('dingtalk')) && !empty(Config::get('dingtalk.logfilename')) ) {
            $this->Monolog = new Logger('Dingtalk');
            $this->Monolog->pushHandler(new StreamHandler(storage_path('logs/'.Config::get('dingtalk.logfilename')) , Logger::DEBUG));
        }
    }

    /**
     * 获取AccessToken
     * @param   string  $appkey 钉钉应用的唯一标识key。
     * @return  array  ["errCode"=>0, "errMsg"=>"ok", "accesstoken"=>"4d54e90d66793c15ac4ca7e91a29904b", "expires"=>1607157220]
     */
    public function getAccessToken($appkey)
    {
        if (Cache::has('Dingtalk_AccessToken_'.$appkey)) {
            $rs = Cache::get('Dingtalk_AccessToken_'.$appkey);
            if ( $rs['expires'] > time() ) {
                return $rs;
            } else {
                return $this->getRefresh($appkey);
            }
        } else {
            return $this->getRefresh($appkey);
        }
    }

    /**
     * 刷新AccessToken行为
     * @param   string  $appkey     应用的唯一标识。
     * @return  array   ["errCode"=>0, "errMsg"=>"ok", "accesstoken"=>"4d54e90d66793c15ac4ca7e91a29904b", "expires"=>1607157220]
     */
    public function getRefresh($appkey = '')
    {
        $keySecret = $this->getKeySecretList();
        if ( array_key_exists('errCode', $keySecret) && $keySecret['errCode'] == 0 ) {
            if (empty($appkey)) {
                //刷新所有AccessToken
                foreach ($keySecret['lists'] as $appkey => $appsecret) {
                    if (Cache::has('Dingtalk_AccessToken_'.$appkey)) {
                        $rs = Cache::get('Dingtalk_AccessToken_'.$appkey);
                        if ( $rs['expires'] > time() + 600 ) {
                            return $rs;
                        } else {
                            return $this->refreshAccessToken($appkey,$appsecret);
                        }
                    } else {
                        // $this->Monolog->ERROR('没有AccessToken缓存，刷新2');//-------------------------------------------------------------写入日志
                        return $this->refreshAccessToken($appkey,$appsecret);
                    }
                }
            } else {
                if (array_key_exists($appkey, $keySecret['lists'])) {
                    return $this->refreshAccessToken($appkey,$keySecret['lists'][$appkey]);
                } else {
                    return ['errCode' => 210002,'errMsg' => '无该AppKey项对应配置信息',];
                }
            }
        } else {
            return $keySecret;
        }
    }

    /**
     * 刷新单个AccessToken
     * @param   string  $appkey     应用的唯一标识。
     * @param   string  $appsecret  应用的密钥。
     * @return  array  ["errCode"=>0, "errMsg"=>"ok", "accesstoken"=>"4d54e90d66793c15ac4ca7e91a29904b", "expires"=>1607157220]
     */
    protected function refreshAccessToken($appkey,$appsecret)
    {
        if ( empty($appkey) || empty($appsecret) ) {
            return ['errCode' => 210001,'errMsg' => '缺少AppKey或AppSecret',];
        } else {
            try {
                $rs = $this->httpClient->get('https://oapi.dingtalk.com/gettoken?appkey='. $appkey .'&appsecret='. $appsecret);
                $getAT = json_decode($rs->getBody()->getContents(),true);
                if (  array_key_exists('errcode', $getAT) ) {
                    if ( $getAT['errcode'] == 0 ) {
                        $accesstoken = [
                            'errCode' => $getAT['errcode'],
                            'errMsg' => $getAT['errmsg'],
                            'accesstoken' => $getAT['access_token'],
                            'expires' => time() -10 + (int)$getAT['expires_in'],
                        ];
                        if ( Cache::forever('Dingtalk_AccessToken_'.$appkey, $accesstoken) ) {
                            return $accesstoken;
                        } else {
                            return ['errCode' => 200002,'errMsg' => '将AccessAoken写入Cache缓存失败'];
                        }
                    } else {
                        return ['errCode' => 210004,'errMsg' => '钉钉全局错误：[ '.$getAT['errcode'].' ]'.$getAT['errmsg'],];
                    }
                } else {
                    return ['errCode' => 210003,'errMsg' => '返回数据中缺少errcode键名',];
                }
            } catch (ConnectException $e) {
                return ['errCode' => 200001,'errMsg' => 'Http请求错误:-> '.substr($e->getMessage(),0,strpos($e->getMessage()," (")),];
            }
        }
    }

    /**
     * 返回{appkey => appsecret}数组列表
     * @param   string  $appkey     应用的唯一标识。
     * @return array   ["errCode"=>0, "errMsg"=>"ok", "lists"=>["appkey1"=> "appsecret1","appkey2"=> "appsecret2"]]
     */
    protected function getKeySecretList()
    {
        //获取 配置中 的 appkey 储存方式
        $configType = Config::get('dingtalk.type', 'config');
        switch ($configType) {
            case 'config':
                return [
                    "errCode" => 0,
                    "errMsg" => "ok",
                    "lists" => Config::get('dingtalk.config'),
                ];
                break;
            case 'database':
#region 未完成代码
                // $this->Monolog->ERROR('缓存中没有这个AppKey对应的AccessToken');
                return [
                    "errCode" => 0,
                    "errMsg" => "ok",
                    "lists" => Config::get('dingtalk.config'),
                ];
                break;
#endregion               
            default:
                return [
                    'errCode' => 210102,
                    'errMsg' => '未知的配置方式',
                ];
                break;
        }      
    }
}
