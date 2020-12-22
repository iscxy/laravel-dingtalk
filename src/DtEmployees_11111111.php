<?php

class DtEmployees
{



    /**
     * 删除员工
     * @param   string  $userid     用户ID
     * @return  array               ['errcode', 'errmsg', "request_id"]
     */
    public function userDelete(string $userid)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/user/delete?access_token='.$token['accesstoken'],[
                "userid" => $userid,
            ]);
        } else {
            return $token;
        }
    }

    /**
     * 更新员工信息
     * @param   array   $data       员工信息
     * @return  array               ['errcode', 'errmsg', "request_id"]
     */
    public function userUpdate(array $data)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/user/update?access_token='.$token['accesstoken'],$data);
        } else {
            return $token;
        }
    }

    /**
     * 获取用户详情
     * @param   string  $userid     用户ID
     * @return  array               ['errcode','errmsg', ...]
     */
    public function userGetById(string $userid)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/user/get?access_token='.$token['accesstoken'],[
                "language" => ( Config::get('app.locale') == "zh_CN" ) ? "zh_CN" : "en_US" ,
                "userid" => $userid,
            ]);
        } else {
            return $token;
        }
    }

    /**
     * 获取员工人数
     * @param   string  $only_active  是否不包含未激活钉钉人数,可用值：false、true
     * @return  array   ['errcode','errmsg', ...]
     */
    public function userCount(string $only_active = 'false')
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/user/count?access_token='.$token['accesstoken'],[
                "only_active" => $only_active,
            ]);
        } else {
            return $token;
        }
    }

    /**
     * 获取管理员列表
     * @return  array   ['errcode','errmsg', 'request_id' => '4f9md9obopn2', 'result' => [['userid' => 'FSDKFJLAK', 'sys_level' => 1],[...]]]
     */
    public function userListAdmin()
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/user/listadmin?access_token='.$token['accesstoken']);
        } else {
            return $token;
        }
    }

    /**
     * 获取未登录钉钉的员工列表
     * @param   string  $query_date 日期，格式：yyyyMMdd
     * @param   string  $is_active  是否是已激活钉钉,可用值：false、true
     * @param   array   $dept_ids   过滤部门ID列表，不传表示查询整个企业,示例：[1,2,3]
     * @param   int     $offset     分页偏移量，从0开始。
     * @param   int     $size       分页大小，最大100
     * @return  array   ['errcode','errmsg', 'result' => ['has_more' => false, 'list' => [...], 'next_cursor' => 1000]]
     */
    public function userInactiveGet(string $query_date,string $is_active = 'false',array $dept_ids = [],int $offset = 0,int $size = 100)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            $data = [
                "is_active" => $is_active,
                "offset" => $offset,
                "size" => $size,
                "query_date" => $query_date,
            ];
            if (!empty($dept_ids)) {
                $data["dept_ids"] = $dept_ids;
            }
            return $this->httpPost('https://oapi.dingtalk.com/topapi/inactive/user/v2/get?access_token='.$token['accesstoken'], $data);
        } else {
            return $token;
        }
    }

    /**
     * 获取管理员通讯录权限范围
     * @param   string  $userid 管理员的userid
     * @return  array   ['errcode','errmsg', 'request_id', 'dept_ids' => [...]]
     */
    public function userAdminScopeById(string $userid)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            return $this->httpPost('https://oapi.dingtalk.com/topapi/user/get_admin_scope?access_token='.$token['accesstoken'],[
                "userid" => $userid,
            ]);
        } else {
            return $token;
        }
    }

    /**
     * 获取部门用户详情
     * @param   int     $dept_id        部门ID，根部门ID为1
     * @param   int     $cursor         分页查询的游标，最开始传0，后续传返回参数中的next_cursor值
     * @param   int     $size           分页大小
     * @param   string  $order_field    部门成员的排序规则，默认：custom， 可先值: entry_asc：代表按照进入部门的时间升序
     *                                                                          entry_desc：代表按照进入部门的时间降序
     *                                                                          modify_asc：代表按照部门信息修改时间升序
     *                                                                          modify_desc：代表按照部门信息修改时间降序
     *                                                                          custom：代表用户定义(未定义时按照拼音)排序
     * @return  array   ['errcode','errmsg', 'result' => [
     *  
     * ]]
     */
    public function userListDeptUserAll(int $dept_id,int $cursor = 0,int $size = 15,string $order_field = 'custom')
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            $data = [
                'language' => ( Config::get('app.locale') == "zh_CN" ) ? "zh_CN" : "en_US" ,
                'order_field' => $order_field,
                'size' => $size,
                'cursor' => $cursor,
                'dept_id' => $dept_id,
            ];
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/user/list?access_token='.$token['accesstoken'], $data);
        } else {
            return $token;
        }
    }

    /**
     * 获取部门用户
     * @param   int     $dept_id        部门ID，根部门ID为1
     * @param   int     $cursor         分页查询的游标，最开始传0，后续传返回参数中的next_cursor值
     * @param   string  $contain        是否返回访问受限的员工,可用值：false、true
     * @param   int     $size           分页大小
     * @param   string  $order_field    部门成员的排序规则，默认：custom， 可先值: entry_asc：代表按照进入部门的时间升序
     *                                                                          entry_desc：代表按照进入部门的时间降序
     *                                                                          modify_asc：代表按照部门信息修改时间升序
     *                                                                          modify_desc：代表按照部门信息修改时间降序
     *                                                                          custom：代表用户定义(未定义时按照拼音)排序
     * @return  array   ['errcode','errmsg', 'result' => ['has_more' => false, 'next_cursor' => 10,'list' => [["name"=>"测试用户2","userid"=>"user100"],[...]] ]]
     */
    public function userListDeptUser(int $dept_id,int $cursor = 0,string $contain = 'false',int $size = 15,string $order_field = 'custom')
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            $data = [
                'language' => ( Config::get('app.locale') == "zh_CN" ) ? "zh_CN" : "en_US" ,
                'order_field' => $order_field,
                'size' => $size,
                'cursor' => $cursor,
                'dept_id' => $dept_id,
                'contain_access_limit' => $contain,
            ];
            return $this->httpPost('https://oapi.dingtalk.com/topapi/user/listsimple?access_token='.$token['accesstoken'], $data);
        } else {
            return $token;
        }
    }

    /**
     * 获取部门用户userid列表
     * @param   int     $dept_id        部门ID，根部门ID为1
     * @return  array   ['errcode','errmsg', 'request_id' => '00000....000', 'result' => ['userid_list' => ["user100", ...] ]]
     */
    public function userListDeptUid(int $dept_id)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            $data = [
                'dept_id' => $dept_id,
            ];
            return $this->httpPost('https://oapi.dingtalk.com/topapi/user/listid?access_token='.$token['accesstoken'], $data);
        } else {
            return $token;
        }
    }

    /**
     * 根据手机号获取userid
     * @param   string  $mobile 用户的手机号
     * @return  array   ['errcode','errmsg','request_id' => '00000....000' , 'result' => ['userid' => "user100"]] ]
     */
    public function userGetUidByMobile(String $mobile)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            $data = [
                'mobile' => $mobile,
            ];
            return $this->httpPost('https://oapi.dingtalk.com/topapi/v2/user/getbymobile?access_token='.$token['accesstoken'], $data);
        } else {
            return $token;
        }
    }

    /**
     * 根据unionid获取用户信息
     * @param   string  $mobile 用户的手机号
     * @return  array   ['errcode','errmsg','request_id' => '00000....000' , 'result' => ['contact_type' => 0,'userid' => "user100"]] ]
     */
    public function userGetByUnionid(String $unionid)
    {
        $token = $this->getAccessToken();
        if ( is_array($token) && array_key_exists('errcode',$token) && $token['errcode'] == 'DT_0' ) {
            $data = [
                'unionid' => $unionid,
            ];
            return $this->httpPost('https://oapi.dingtalk.com/topapi/user/getbyunionid?access_token='.$token['accesstoken'], $data);
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