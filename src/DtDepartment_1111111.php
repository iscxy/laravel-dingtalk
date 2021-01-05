<?php

namespace Iscxy\Dingtalk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;


/**
 * 部门管理
 * 文档网址：https://ding-doc.dingtalk.com/document#/org-dev-guide/create-a-department-v2
 */
class DtDepartment_1111111
{
    protected $httpClient;
    protected $accessToken;
    protected $apiurl;

    public function __construct($accesstoken)
    {
        $this->httpClient = new Client([
            'timeout'  => 5.0,
            'verify' => false,
        ]);
        $this->apiurl = 'https://oapi.dingtalk.com/topapi/v2';
        $this->accessToken = $accesstoken;
    }

    /**
     * 创建部门
     * @param   array   $data       部门信息
     * @return  array               ['errcode', 'errmsg', "request_id", "result"=> ['dept_id' => '...']]
     */
    public function deptCreate(array $data)
    {
        return $this->httpPost($this->apiurl.'/department/create?access_token='.$this->accessToken,$data);
    }

    /**
     * 更新部门
     * @param   array   $data       部门信息
     * @return  array               ['errcode', 'errmsg', "request_id"]
     */
    public function deptUpdate(array $data)
    {
        return $this->httpPost($this->apiurl.'/department/update?access_token='.$this->accessToken,$data);
    }

    /**
     * 删除部门
     * @param   string   $dept_id   部门ID
     * @return  array               ['errcode', 'errmsg', "request_id"]
     */
    public function deptDelete(string $dept_id)
    {
        return $this->httpPost($this->apiurl.'/department/update?access_token='.$this->accessToken,[
            'dept_id' => $dept_id,
        ]);
    }

    /**
     * 获取部门详情
     * @param   string  $dept_id    部门ID
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function deptGetById(int $dept_id)
    {
        return $this->httpPost($this->apiurl.'/department/get?access_token='.$this->accessToken,[
            "language" => ( Config::get('app.locale') == "zh_CN" ) ? "zh_CN" : "en_US" ,
            "dept_id" => $dept_id,
        ]);
    }

    /**
     * 获取部门列表
     * @param   string  $dept_id    部门ID
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function deptList(string $dept_id)
    {
        return $this->httpPost($this->apiurl.'/department/listsub?access_token='.$this->accessToken,[
            "language" => ( Config::get('app.locale') == "zh_CN" ) ? "zh_CN" : "en_US" ,
            "dept_id" => $dept_id,
        ]);
    }

    /**
     * 获取子部门ID列表
     * @param   string  $dept_id    部门ID
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function deptListId(string $dept_id)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost($this->apiurl.'/department/listsubid?access_token='.$this->accessToken,[
                "dept_id" => $dept_id,
            ]);
        } else {
            return $token;
        }
    }

    /**
     * 获取指定用户的所有父部门列表
     * @param   string  $userid     用户ID
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function deptListPDByUser(string $userid)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost($this->apiurl.'/department/listparentbyuser?access_token='.$this->accessToken,[
                "userid" => $userid,
            ]);
        } else {
            return $token;
        }
    }

    /**
     * 获取指定部门的所有父部门列表
     * @param   string  $dept_id    部门ID
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function deptListPDByDept(string $dept_id)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost($this->apiurl.'/department/listparentbydept?access_token='.$this->accessToken,[
                "dept_id" => $dept_id,
            ]);
        } else {
            return $token;
        }
    }

    /**
     * GET请求
     * @param   string  $url    请求地址
     * @return  array   ['errcode','errmsg', ...]
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
     * @param   string  $url    请求地址
     * @param   array   $data   请求数据
     * @return  array   ['errcode','errmsg', ...]
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
}