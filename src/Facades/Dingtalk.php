<?php

namespace Iscxy\Dingtalk\Facades;

use Illuminate\Support\Facades\Facade;

class Dingtalk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dingtalk';
    }
}