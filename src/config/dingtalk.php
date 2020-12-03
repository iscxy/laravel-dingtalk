<?php

return [
    /**
     * 参数配置方式
     * 
     * 支持："config", "database"
     */
    'type' => env('dingtalk_config_type', 'config'),


    /*格式： 'appkey' => 'appsecret', */
    'config' => [
        'dingfrnzjsaegbrkitfb' => '-vPzc1m9YXPS8-uucOHT0i7WwVC689h606T74KVp39smIIoqwsabyrXN2tnzy-HZ',
        'dingfrnzjsae1111' => '-vPzc1m9YXPS8-uucOHT0i7WwVC683223Vp39smIIoqwsabyrXN2tnzy-HZ',
    ],

    'database' => [
        'table' => env('dingtalk_database_table', 'system_config'),
        'group' => env('dingtalk_database_group', 'dingtalk'),
        // 'key' => env('dingtalk_database_key', ''),
        // 'value' => env('dingtalk_database_value', ''),
    ],
];
