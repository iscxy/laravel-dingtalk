<?php

namespace Iscxy\Dingtalk;

use GuzzleHttp\Exception\ConnectException;

/**
 * 角色管理 V2
 * 文档网址：https://ding-doc.dingtalk.com/document#/org-dev-guide/create-a-user-v2
 */
class ABEmployee
{
    protected $httpClient;
    protected $accessToken;
    protected $apiurl;

    /**
     * @param   string  $accesstoken    AccessToken
     * @param   object  $client         GuzzleHttp客户端
     */
    public function __construct($accesstoken,$client)
    {
        $this->httpClient = $client; 
        $this->accessToken = $accesstoken;
        $this->apiurl = 'https://oapi.dingtalk.com/topapi/v2';
    }

    /**
     * GET请求
     * @param   string  $url            请求地址
     * @return  array                   ['errcode','errmsg', ...]
     */
    public function httpGet(string $url)
    {
        try {
            $rs = $this->httpClient->request('GET', $url);
            $rsa = json_decode($rs->getBody()->getContents(),true);
            $rsa['errcode'] = 'DT_'.$rsa['errcode'];
            return $rsa;
        } catch (ConnectException $e) {
            return ['errcode' => 200001,'errmsg' => 'Http请求错误:-> '.substr($e->getMessage(),0,strpos($e->getMessage()," (")),];
        }
    }

    /**
     * POST请求
     * @param   string  $url            请求地址
     * @param   array   $data           请求数据
     * @return  array                   ['errcode','errmsg', ...]
     */
    public function httpPost(string $url,array $data = [])
    {
        try {
            $rs = $this->httpClient->request('POST', $url, ['form_params' => $data]);
            $rsa = json_decode($rs->getBody()->getContents(),true);
            $rsa['errcode'] = 'DT_'.$rsa['errcode'];
            return $rsa;
        } catch (ConnectException $e) {
            return ['errcode' => 200001,'errmsg' => 'Http请求错误:-> '.substr($e->getMessage(),0,strpos($e->getMessage()," (")),];
        }
    }

    /**
     * 新增员工
     * @param   array   $data           员工信息
     * @return  array                   ['errcode', 'errmsg', "request_id", "result"=> ['userid' => '...']]
     */
    public function userCreate(array $data)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost($this->apiurl.'/user/create?access_token='.$token['accesstoken'],$data);
        } else {
            return $token;
        }
    }









}