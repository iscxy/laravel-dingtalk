<?php

namespace Iscxy\Dingtalk;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class DtDepartment
{


    /**
     * 
     * @return array   ['errcode','errmsg', 'accesstoken' => 'e6509af20e5f3f9f813f6fe35c990add', 'expires' => 1607910603]
     */
    public function getAccessToken()
    {
        if (Cache::has('DingTalk_AccessToken_'.$this->appkey)) {
            $rs = Cache::get('DingTalk_AccessToken_'.$this->appkey);
            return $rs;
        }
    }

    /**
     * 创建部门
     * @param   array   $data       部门信息
     * @return  array               ['errcode', 'errmsg', "request_id", "result"=> ['dept_id' => '...']]
     */
    public function deptCreate(array $data)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/department/create?access_token='.$token['accesstoken'],$data);
        } else {
            return $token;
        }
    }

    /**
     * 更新部门
     * @param   array   $data       部门信息
     * @return  array               ['errcode', 'errmsg', "request_id"]
     */
    public function deptUpdate(array $data)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/department/update?access_token='.$token['accesstoken'],$data);
        } else {
            return $token;
        }
    }

    /**
     * 删除部门
     * @param   string   $dept_id   部门ID
     * @return  array               ['errcode', 'errmsg', "request_id"]
     */
    public function deptDelete(string $dept_id)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/department/update?access_token='.$token['accesstoken'],[
                'dept_id' => $dept_id,
            ]);
        } else {
            return $token;
        }
    }

    /**
     * 获取部门详情
     * @param   string  $dept_id    部门ID
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function deptGetById(string $dept_id)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/user/get?access_token='.$token['accesstoken'],[
                "language" => ( Config::get('app.locale') == "zh_CN" ) ? "zh_CN" : "en_US" ,
                "dept_id" => $dept_id,
            ]);
        } else {
            return $token;
        }
    }

    /**
     * 获取部门列表
     * @param   string  $dept_id    部门ID
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function deptList(string $dept_id)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/department/listsub?access_token='.$token['accesstoken'],[
                "language" => ( Config::get('app.locale') == "zh_CN" ) ? "zh_CN" : "en_US" ,
                "dept_id" => $dept_id,
            ]);
        } else {
            return $token;
        }
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
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/department/listsubid?access_token='.$token['accesstoken'],[
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
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/department/listparentbyuser?access_token='.$token['accesstoken'],[
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
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/department/listparentbydept?access_token='.$token['accesstoken'],[
                "dept_id" => $dept_id,
            ]);
        } else {
            return $token;
        }
    }
}