<?php

namespace Iscxy\Dingtalk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

/**
 * 通讯录管理
 * 文档网址：https://ding-doc.dingtalk.com/document#/org-dev-guide/address-book-permissions
 */
class DtContacts
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout'  => 5.0,
            'verify' => false,
        ]);
    }

    /**
     * 
     * @param   string  $appkey    应用的唯一标识key
     * @return array ['errcode' => 0, 'errmsge' => 'ok']
     */
    public function getAccessToken($appkey)
    {
        if (Cache::has('Dingtalk_AccessToken_'.$appkey)) {
            $rs = Cache::get('Dingtalk_AccessToken_'.$appkey);
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
     * 
     * @return array 
     */
    public function authScopes()
    {   
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 0 ) {
            return $this->httpGet('https://oapi.dingtalk.com/auth/scopes?access_token='.$token['accesstoken']);
        } else {
            return $token;
        }
    }

    /**
     * 创建用户
     */
    public function userCreate()
    {

    }

    /**
     * 删除用户
     */
    public function userDelete()
    {
        # code ...
    }

    /**
     * 更新用户
     */
    public function userUpdate()
    {
        # code ...
    }

    /**
     * 获取用户
     */
    public function userGet($userid)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 0 ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/user/get?access_token='.$token['accesstoken'],[
                "language" => Config::get('app.locale', 'zh_CN'),
                "userid" => $userid,
            ]);
        } else {
            return $token;
        }
    }

    /**
     * GET请求
     * @param   string  $url    请求地址
     * @return  array   ['errcode' => 'Dt_0', 'errmsg' => 'Http请求 ', ...]
     */
    public function httpGet($url)
    {
        try {
            $rs = $this->httpClient->request('GET', $url);
            $rsa = json_decode($rs->getBody()->getContents(),true);
            $rsa['errcode'] = 'Dt_'.$rsa['errcode'];
            return $rsa;
        } catch (ConnectException $e) {
            return ['errcode' => 200001,'errmsg' => 'Http请求错误:-> '.substr($e->getMessage(),0,strpos($e->getMessage()," (")),];
        }
    }

    /**
     * POST请求
     * @param   string  $url    请求地址
     * @param   array   $data   请求数据
     * @return  array   ['errcode' => 'Dt_0', 'errmsg' => 'Http请求 ', ...]
     */
    public function httpPost($url, $data = '')
    {
        try {
            $rs = $this->httpClient->request('POST', $url, ['form_params' => $data]);
            $rsa = json_decode($rs->getBody()->getContents(),true);
            $rsa['errcode'] = 'Dt_'.$rsa['errcode'];
            return $rsa;
        } catch (ConnectException $e) {
            return ['errcode' => 200001,'errmsg' => 'Http请求错误:-> '.substr($e->getMessage(),0,strpos($e->getMessage()," (")),];
        }
    }
}