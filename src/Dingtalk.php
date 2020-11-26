<?php

namespace Iscxy\Dingtalk;

class Dingtalk
{
    protected $accesstoken;

    public function __construct()
    {
        $this->getAccessToken();
    }

    public function getAccessToken()
    {
        return "";
    }

}
