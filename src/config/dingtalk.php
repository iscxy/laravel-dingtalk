<?php

return [
    /**
     * 参数配置方式
     * 
     * 支持："config", "database"
     */
    'type' => env('dingtalk_config_type', 'config'),

    'config' => [
        'appkey' => env('dingtalk_appkey', ''),
        'appsecret' => env('dingtalk_appsecret', ''),
    ],

    'database' => [
        'table' => env('dingtalk_database_table', 'system_config'),
        'group' => env('dingtalk_database_group', 'dingtalk'),
        // 'key' => env('dingtalk_database_key', ''),
        // 'value' => env('dingtalk_database_value', ''),
    ],
];
