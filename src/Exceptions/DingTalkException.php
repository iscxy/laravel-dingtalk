<?php

namespace Iscxy\Dingtalk\Exceptions;

class DingTalkException extends \Exception
{

    public function jsonErrorMessage()
    {
        return json_encode([
            'errcode' => $this->getCode(),
            'errmsg' => $this->getMessage(),
        ]);
    }

    public function arrayErrorMessage()
    {
        return [
            'errcode' => $this->getCode(),
            'errmsg' => $this->getMessage(),
        ];
    }
}