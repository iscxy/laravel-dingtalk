<?php

namespace Iscxy\Dingtalk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Iscxy\Dingtalk\Exceptions\DingTalkException;

class Dingtalk
{

    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout'  => 5.0,
            'verify' => false,
        ]);
    }

#region AccessToken

    /**
     * 刷新单个AccessToken
     * @param   string  $appkey     应用的唯一标识。
     * @param   string  $appsecret  应用的密钥。
     * @return  array               ["errcode", "errmsg", "accesstoken"=>"...", "expires"=>...]
     */
    protected function refreshAccessToken($appkey,$appsecret)
    {
        if ( empty($appkey) || empty($appsecret) ) {
            return ['errcode' => 210001,'errmsg' => '缺少AppKey或AppSecret',];
        } else {
            try {
                $rs = $this->httpClient->get('https://oapi.dingtalk.com/gettoken?appkey='. $appkey .'&appsecret='. $appsecret);
                $getAT = json_decode($rs->getBody()->getContents(),true);
                if (  is_array($getAT) && array_key_exists('errcode', $getAT) ) {
                    if ( $getAT['errcode'] == 0 ) {
                        $accesstoken = [
                            'errcode' => $getAT['errcode'],
                            'errmsg' => $getAT['errmsg'],
                            'accesstoken' => $getAT['access_token'],
                            'expires' => time() + (int)$getAT['expires_in'],
                        ];
                        if ( Cache::forever('DingTalk_AccessToken_'.$appkey, $accesstoken) ) {
                            return $accesstoken;
                        } else {
                            return ['errcode' => 200002,'errmsg' => '将AccessAoken写入Cache缓存失败'];
                        }
                    } else {
                        return ['errcode' => 210101,'errmsg' => '钉钉全局错误：[ '.$getAT['errcode'].' ]'.$getAT['errmsg'],];
                    }
                } else {
                    return ['errcode' => 210003,'errmsg' => '返回数据中缺少errcode键名',];
                }
            } catch (ConnectException $e) {
                return ['errcode' => 200001,'errmsg' => 'Http请求错误:-> '.substr($e->getMessage(),0,strpos($e->getMessage()," (")),];
            }
        }
    }

    /**
     * 刷新AccessToken行为
     * @param   string  $appkey     应用的唯一标识。
     * @return  array               ["errcode", "errmsg", "accesstoken"=>"...", "expires"=>...]
     */
    public function getRefresh(string $appkey = '')
    {
        try {
            $keySecret = $this->getKeySecretList();
            if ( is_array($keySecret) && array_key_exists('errcode', $keySecret) && $keySecret['errcode'] == 0 ) {
                if ( empty($appkey) ) {
                    //刷新所有AccessToken
                    foreach ($keySecret['lists'] as $appkey => $appsecret) {
                        if (Cache::has('DingTalk_AccessToken_'.$appkey)) {
                            $rs = Cache::get('DingTalk_AccessToken_'.$appkey);
                            if ( $rs['expires'] <= time() ) {
                                $this->refreshAccessToken($appkey,$appsecret);
                            }
                        } else {
                            $this->refreshAccessToken($appkey,$appsecret);
                        }
                    }
                } else {
                    if (array_key_exists($appkey, $keySecret['lists'])) {
                        $rat = $this->refreshAccessToken($appkey,$keySecret['lists'][$appkey]);
                        if ( $rat['errcode'] == 0 ) {
                            return $rat;
                        } else {
                            throw new DingTalkException($rat['errmsg'],$rat['errcode']);
                        }
                    } else {
                        throw new DingTalkException('无该AppKey项对应配置信息',210002);
                    }
                }
            } else {
                throw new DingTalkException('获取Appkey和AppSecret列表失败',210005);
            }
        } catch (DingTalkException $e) {
            return $e->arrayErrorMessage();
        }
    }

    /**
     * 获取AccessToken
     * @param   string  $appkey     钉钉应用的唯一标识key。
     * @return  array               ["errcode", "errmsg", "accesstoken"=>"...", "expires"=>...]
     */
    public function getAccessToken(string $appkey)
    {
        if (Cache::has('DingTalk_AccessToken_'.$appkey)) {
            $rs = Cache::get('DingTalk_AccessToken_'.$appkey);
            if ( $rs['expires'] > time()+5 ) {
                return $rs;
            } else {
                return $this->getRefresh($appkey);
            }
        } else {
            return $this->getRefresh($appkey);
        }
    }

    /**
     * 获取通讯录权限范围
     * @param   string  $appkey     钉钉应用的唯一标识key。
     * @return  array               ['errcode','errmsg', 'auth_user_field' => [...], 'auth_org_scopes' => ['authed_user' => [...], 'authed_dept' => [...]]]
     */
    public function abAuthScopes(string $appkey)
    {
        $token = $this->getAccessToken($appkey);
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == '0' ) {
            try {
                $rs = $this->httpClient->get('https://oapi.dingtalk.com/auth/scopes?access_token='. $token['accesstoken']);
                $rsarr = json_decode($rs->getBody()->getContents(),true);
                if ( is_array($rsarr) && array_key_exists('errcode',$rsarr) && $rsarr['errcode'] == '0' ) {
                    return $rsarr;
                } else {
                    return ['errcode' => 210101,'errmsg' => '钉钉全局错误：[ '.$rsarr['errcode'].' ]'.$rsarr['errmsg'],];
                }
            } catch (ConnectException $e) {
                return ['errcode' => 200001,'errmsg' => 'Http请求错误:-> '.substr($e->getMessage(),0,strpos($e->getMessage()," (")),];
            }
        } else {
            return $token;
        }
    }

#endregion



#region 未完成代码


    /**
     * 返回{appkey => appsecret}数组列表
     * @param   string  $appkey     应用的唯一标识。
     * @return array   ["errcode", "errmsg", "lists"=>["appkey1"=> "appsecret1","appkey2"=> "appsecret2"]]
     */
    protected function getKeySecretList()
    {
        //获取 配置中 的 appkey 储存方式
        $configType = Config::get('dingtalk.type', 'config');
        switch ($configType) {
            case 'config':
                return [
                    "errcode" => 0,
                    "errmsg" => "ok",
                    "lists" => Config::get('dingtalk.config'),
                ];
                break;
            case 'database':
                // $this->Monolog->ERROR('缓存中没有这个AppKey对应的AccessToken');
                return [
                    "errcode" => 0,
                    "errmsg" => "ok",
                    "lists" => Config::get('dingtalk.config'),
                ];
                break; 
            default:
                throw new DingTalkException('未知的配置方式',210004);
                break;
        }      
    }
#endregion






#region 调用其它类
    /**
     * ABRole.php   角色管理
     * 文档网址：https://ding-doc.dingtalk.com/document#/org-dev-guide/list-roles
     */
    /*
    public function classRole(string $appkey)
    {
        try {  
            $token = $this->getAccessToken($appkey);
            if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == '0' ) {
                return new ABRole($token['accesstoken'],$this->httpClient);
            } else {
                throw new DingTalkException('未知的配置方式',210004);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    /**
     * ABEmployee.php   用户管理
     * 文档网址：https://ding-doc.dingtalk.com/document#/org-dev-guide/create-a-user-v2
     */
    /*
    public function classEmployee(string $appkey)
    {
        $token = $this->getAccessToken($appkey);
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == '0' ) {
            return new ABEmployee($token['accesstoken'],$this->httpClient);
        } else {
            throw new DingTalkException('未知的配置方式',210004);
        }
    }
    */
#endregion



}
