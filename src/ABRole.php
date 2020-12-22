<?php

namespace Iscxy\Dingtalk;

use GuzzleHttp\Exception\ConnectException;

/**
 * 角色管理
 * 文档网址：https://ding-doc.dingtalk.com/document#/org-dev-guide/list-roles
 */
class ABRole
{
    protected $httpClient;
    protected $accessToken;
    protected $apiurl;

    /**
     * @param   string     $accesstoken     AccessToken
     * @param   object     $client          GuzzleHttp客户端
     */
    public function __construct($accesstoken,$client)
    {
        $this->httpClient = $client; 
        $this->accessToken = $accesstoken;
        $this->apiurl = 'https://oapi.dingtalk.com';
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

    /**
     * 获取角色列表
     * @param   int     $size       分页大小，默认值20，最大值200。
     * @param   int     $offset     偏移量，偏移量从0开始。
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function roleList(int $size = 20,int $offset = 0)
    {
        return $this->httpPost($this->apiurl.'/topapi/role/list?access_token='.$this->accessToken,[
            "size" => $size,
            "offset" => $offset,
        ]);
    }

    /**
     * 获取指定角色的员工列表
     * @param   int     $role_id    角色ID
     * @param   int     $size       分页大小，默认值20，最大值200。
     * @param   int     $offset     偏移量，偏移量从0开始。
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function roleSimpleList(int $role_id,int $size = 20,int $offset = 0)
    {
        return $this->httpPost($this->apiurl.'/topapi/role/simplelist?access_token='.$this->accessToken,[
            "role_id" => $role_id,
            "size" => $size,
            "offset" => $offset,
        ]);
    }

    /**
     * 获取角色组
     * @param   int     $group_id   角色组的ID
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function roleGetRoleGroup(int $group_id)
    {
        return $this->httpPost($this->apiurl.'/topapi/role/getrolegroup?access_token='.$this->accessToken,[
            "group_id" => $group_id,
        ]);
    }

    /**
     * 获取角色详情
     * @param   int     $roleId     角色ID
     * @return  array               ['errcode', 'errmsg', 'request_id', 'result'=>[...]]
     */
    public function roleGetRole(int $roleId)
    {
        return $this->httpPost($this->apiurl.'/topapi/role/getrole?access_token='.$this->accessToken,[
            "roleId" => $roleId,
        ]);
    }

    /**
     * 创建角色组
     * @param   string  $name       角色组名称
     * @return  array               ['errcode', 'errmsg', 'groupId']
     */
    public function roleAddRoleGroup(string $name)
    {
        return $this->httpPost($this->apiurl.'/role/add_role_group?access_token='.$this->accessToken,[
            "name" => $name,
        ]);
    }

    /**
     * 创建角色
     * @param   string  $roleName   角色名称
     * @param   int     $groupId    角色组ID
     * @return  array               ['errcode', 'errmsg', 'roleId']
     */
    public function roleAddRole(string $roleName,int $groupId)
    {
        return $this->httpPost($this->apiurl.'/role/add_role?access_token='.$this->accessToken,[
            "roleName" => $roleName,
            "groupId" => $groupId,
        ]);
    }

    /**
     * 批量增加员工角色
     * @param   string  $roleIds    角色roleId列表,多个roleId用英文逗号（,）分隔，最多可传20个。
     * @param   string  $userIds    员工的userId，多个userId用英文逗号（,）分隔，最多可传20个。
     * @return  array               ['errcode', 'errmsg', 'request_id']
     */
    public function roleAddRolesForEmps(string $roleIds,string $userIds)
    {
        return $this->httpPost($this->apiurl.'/topapi/role/addrolesforemps?access_token='.$this->accessToken,[
            "roleIds" => $roleIds,
            "userIds" => $userIds,
        ]);
    }

    /**
     * 更新角色
     * @param   int     $roleId     角色ID
     * @param   string  $roleName   角色名称
     * @return  array               ['errcode', 'errmsg']
     */
    public function roleUpdateRole(int $roleId,string $roleName)
    {
        return $this->httpPost($this->apiurl.'/role/update_role?access_token='.$this->accessToken,[
            "roleId" => $roleId,
            "roleName" => $roleName,
        ]);
    }

    /**
     * 删除角色
     * @param   int     $role_id     角色ID
     * @return  array               ['errcode', 'errmsg', 'request_id']
     */
    public function roleDeleteRole(int $role_id)
    {
        return $this->httpPost($this->apiurl.'/topapi/role/deleterole?access_token='.$this->accessToken,[
            "role_id" => $role_id,
        ]);
    }

    /**
     * 批量删除员工角色
     * @param   string  $roleIds    角色roleId列表,多个roleId用英文逗号（,）分隔，最多可传20个。
     * @param   string  $userIds    员工的userId，多个userId用英文逗号（,）分隔，最多可传20个。
     * @return  array               ['errcode', 'errmsg', 'request_id']
     */
    public function roleRemoveRolesForEmps(string $roleIds,string $userIds)
    {
        return $this->httpPost($this->apiurl.'/topapi/role/removerolesforemps?access_token='.$this->accessToken,[
            "roleIds" => $roleIds,
            "userIds" => $userIds,
        ]);
    }

    /**
     * 设定角色成员管理范围
     * @param   string  $userid     员工在企业中的userid
     * @param   int     $role_id    角色ID。
     * @param   array   $dept_ids   部门ID列表数。最多50个，不传则设置范围为所有人
     * @return  array               ['errcode', 'errmsg', 'request_id']
     */
    public function roleRolesForEmps(string $userid,int $role_id,array $dept_ids = [])
    {
        $data = [
            "userid" => $userid,
            "role_id" => $role_id,
        ];
        if (!empty($dept_ids)) {
            $data['dept_ids'] = $dept_ids;
        }
        return $this->httpPost($this->apiurl.'/topapi/role/scope/update?access_token='.$this->accessToken,$data);
    }
}